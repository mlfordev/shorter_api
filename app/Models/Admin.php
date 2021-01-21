<?php

namespace App\Models;

use Phact\Orm\Fields\CharField;
use Phact\Orm\Fields\DateTimeField;
use Phact\Orm\Fields\EmailField;
use Phact\Orm\Model;

/**
 * Class User
 * @package App\Models
 */
class Admin extends Model
{
    /** @var Admin[] */
    private static $admins = [];

    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'admins';
    }

    /**
     * @return string[][]
     */
    public static function getFields()
    {
        return [
            'login' => [
                'class' => EmailField::class,
                'label' => 'Логин',
            ],
            'password' => [
                'class' => CharField::class,
                'label' => 'Пароль',
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
        return (string)$this->login;
    }

    /**
     * @param string $raw
     * @return bool
     */
    public function passwordVerify($raw = ''): bool
    {
        return password_verify($raw, $this->password);
    }

    /**
     * @param $login
     * @return Admin
     */
    public static function getByLoginOrCreate(?string $login): self
    {
        if (empty(static::$admins[$login])) {
            /** @var Admin $admin */
            $admin = static::objects()->filter(['login' => $login])->limit(1)->get();
            static::$admins[$login] = $admin ?? new self();
        }
        return static::$admins[$login];
    }
}