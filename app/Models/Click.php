<?php

namespace App\Models;


use Phact\Orm\Fields\CharField;
use Phact\Orm\Fields\DateTimeField;
use Phact\Orm\Fields\EmailField;
use Phact\Orm\Fields\ForeignField;
use Phact\Orm\Fields\TextField;
use Phact\Orm\Model;

class Click extends Model
{

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'clicks';
    }

    /**
     * @return string[][]
     */
    public static function getFields()
    {
        return [
            'remote_addr' => [
                'class' => CharField::class,
                'label' => 'Удаленный адрес',
                'null' => true,
            ],
            'remote_host' => [
                'class' => CharField::class,
                'label' => 'Удаленный хост',
                'null' => true,
            ],
            'http_user_agent' => [
                'class' => CharField::class,
                'label' => 'User agent',
                'null' => true,
            ],
            'http_host' => [
                'class' => CharField::class,
                'label' => 'Удаленный хост(http_host)',
                'null' => true,
            ],
            'http_referer' => [
                'class' => TextField::class,
                'label' => 'Адрес страницы перехода',
                'null' => true,
            ],
            'short_link' => [
                'class' => ForeignField::class,
                'modelClass' => ShortLink::class,
                'label' => 'Короткая ссылка',
            ],
            'created_at' => [
                'class' => DateTimeField::class,
                'autoNowAdd' => true,
                'editable' => false,
                'label' => 'Дата добавления',
            ],
            'updated_at' => [
                'class' => DateTimeField::class,
                'autoNow' => true,
                'editable' => false,
                'label' => 'Дата изменения',
                'null' => true,
            ],
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->short_link_id;
    }

}