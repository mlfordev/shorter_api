<?php

namespace Phact\Orm;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Comparator;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index as DBALIndex;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Phact\Orm\Index as PhactIndex;
use Phact\Orm\Configuration\ConfigurationProvider;
use Phact\Orm\Fields\Field;
use Phact\Orm\Fields\ManyToManyField;

class TableManager
{
    public $checkExists = true;
    public $addFields = true;
    public $processFk = false;

    /**
     * @param array $models
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Phact\Exceptions\UnknownPropertyException
     * @throws \Phact\Exceptions\InvalidConfigException
     */
    public function create($models = [])
    {
        foreach ($models as $model) {
            $this->createModelTable($model);
        }
        return true;
    }

    /**
     * @param array $models
     * @param null $mode @deprecated
     * @return bool
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Phact\Exceptions\UnknownPropertyException
     * @throws \Phact\Exceptions\InvalidConfigException
     */
    public function drop($models = [], $mode = null)
    {
        foreach ($models as $model) {
            $this->dropModelTable($model, $mode);
        }
        return true;
    }

    /**
     * @param $model Model
     * @return \Doctrine\DBAL\Schema\AbstractSchemaManager
     * @throws \Phact\Exceptions\InvalidConfigException
     */
    public function getSchemaManager($model)
    {
        $connectionName = $model->getConnectionName();
        $configuration = ConfigurationProvider::getInstance()->getManager();
        $connectionManager = $configuration->getConnectionManager();
        $connection = $connectionManager->getConnection($connectionName);
        return $connection->getSchemaManager();
    }

    /**
     * @param $model Model
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Phact\Exceptions\UnknownPropertyException
     * @throws \Phact\Exceptions\InvalidConfigException
     */
    public function createModelTable($model)
    {
        $tableName = $model->getTableName();
        $columns = $this->createColumns($model);

        $dbalIndexes = $this->convertIndexes($model->getIndexes());

        $table = new Table($tableName, $columns, $dbalIndexes);
        $schemaManager = $this->getSchemaManager($model);
        if (!$schemaManager->tablesExist([$tableName])) {
            $schemaManager->createTable($table);
        } else {
            $tableExists = $schemaManager->listTableDetails($tableName);
            $comparator = new Comparator();
            if ($diff = $comparator->diffTable($tableExists, $table)) {
                $schemaManager->alterTable($diff);
            }
        }
        $this->createM2MTables($model);
    }

    /**
     * @param $model Model
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Phact\Exceptions\UnknownPropertyException
     * @throws \Phact\Exceptions\InvalidConfigException
     */
    public function createM2MTables($model)
    {
        $schemaManager = $this->getSchemaManager($model);
        foreach ($model->getFieldsManager()->getFields() as $field) {
            if ($field instanceof ManyToManyField && !$field->getThrough()) {
                $tableName = $field->getThroughTableName();
                $columns = [];

                $toModelClass = $field->modelClass;
                /** @var $toModel Model */
                $toModel = new $toModelClass();

                $to = $field->getTo();
                $toColumnName = $field->getThroughTo();
                $toField = $toModel->getField($to);
                $toColumnOptions = $toField->getColumnOptions();
                if (isset($toColumnOptions['autoincrement'])) {
                    unset($toColumnOptions['autoincrement']);
                }
                $columns[] = new Column($toColumnName, Type::getType($toField->getType()), $toColumnOptions);

                $from = $field->getFrom();
                $fromColumnName = $field->getThroughFrom();
                $fromField = $model->getField($from);
                $fromColumnOptions = $fromField->getColumnOptions();
                if (isset($fromColumnOptions['autoincrement'])) {
                    unset($fromColumnOptions['autoincrement']);
                }
                $columns[] = new Column($fromColumnName, Type::getType($toField->getType()), $fromColumnOptions);

                $fk = [];
                if ($this->processFk) {
                    $fk[] = new ForeignKeyConstraint([$toColumnName], $toModel->getTableName(), [$to]);
                    $fk[] = new ForeignKeyConstraint([$fromColumnName], $toModel->getTableName(), [$from]);
                }
                $table = new Table($tableName, $columns, [], $fk);
                if (!$schemaManager->tablesExist([$tableName])) {
                    $schemaManager->createTable($table);
                }
            }
        }
    }

    public function createColumns($model)
    {
        $fieldsManager = $model->getFieldsManager();
        $fields = $fieldsManager->getFields();
        $columns = [];
        /** @var Field $field */
        foreach ($fields as $field) {
            $attribute = $field->getAttributeName();
            if ($attribute && !$field->virtual) {
                $columnName = $attribute;
                $column = new Column($columnName, Type::getType($field->getType()), $field->getColumnOptions());
                $columns[$columnName] = $column;
            }
        }
        return $columns;
    }

    /**
     * @param $model Model
     * @param int $mode @deprecated
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Phact\Exceptions\UnknownPropertyException
     * @throws \Phact\Exceptions\InvalidConfigException
     */
    public function dropModelTable($model, $mode = null)
    {
        $this->dropM2MTables($model, $mode);
        $this->dropModelForeignKeys($model);

        $tableName = $model->getTableName();
        $schemaManager = $this->getSchemaManager($model);
        if ($schemaManager->tablesExist([$tableName])) {
            $this->getSchemaManager($model)->dropTable($tableName);
        }
    }

    /**
     * @param $model Model
     * @param $mode @deprecated
     * @throws \Phact\Exceptions\InvalidConfigException
     */
    public function dropM2MTables($model, $mode = null)
    {
        $schemaManager = $this->getSchemaManager($model);
        foreach ($model->getFieldsManager()->getFields() as $field) {
            if ($field instanceof ManyToManyField && !$field->getThrough()) {
                $tableName = $field->getThroughTableName();
                if ($schemaManager->tablesExist([$tableName])) {
                    $schemaManager->dropTable($tableName);
                }
            }
        }
    }

    /**
     * @param $model Model
     * @throws \Phact\Exceptions\InvalidConfigException
     */
    public function dropModelForeignKeys($model)
    {
        $tableName = $model->getTableName();
        $schemaManager = $this->getSchemaManager($model);
        foreach ($schemaManager->listTableForeignKeys($tableName) as $constraint) {
            $schemaManager->dropForeignKey($constraint, $tableName);
        }
    }

    /**
     * @param PhactIndex[] $indexes
     * @return DBALIndex[]
     * @throws \Exception
     */
    public function convertIndexes(array $indexes)
    {
        $dbalIndexes = [];
        foreach ($indexes as $index) {
            if (!($index instanceof PhactIndex)) {
                throw new \Exception('Invalid index object. Expected ' . PhactIndex::class);
            }
            $dbalIndexes[] = new DBALIndex(
                $index->getIndexName(),
                $index->getColumns(),
                $index->isUnique(),
                $index->isPrimary(),
                $index->getFlags(),
                $index->getOptions()
            );
        }
        return $dbalIndexes;
    }
}