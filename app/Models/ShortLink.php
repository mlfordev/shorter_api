<?php

namespace App\Models;

use App\Core\HttpRequest;
use App\Lib\Helper;
use Phact\Orm\Fields\DateTimeField;
use Phact\Orm\Fields\HasManyField;
use Phact\Orm\Fields\PositionField;
use Phact\Orm\Fields\TextField;
use Phact\Orm\Model;

/**
 * Class ShortLink
 * @package App\Models
 */
class ShortLink extends Model
{
    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'short_links';
    }

    /**
     * @return array[]
     */
    public static function getFields(): array
    {
        return [
            'url' => [
                'class' => TextField::class,
                'label' => 'Адрес ссылки',
            ],
            'clicks' => [
                'class' => HasManyField::class,
                'modelClass' => Click::class,
                'label' => 'Переходы',
                'to' => 'short_link_id',
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
            'position' => [
                'class' => PositionField::class,
                'editable' => false,
                'relations' => [],
            ],
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getShortCode();
    }

    /**
     * @return string
     */
    public function getShortCode(): string
    {
        return Helper::convertDecToX62($this->id);
    }

    /**
     * @param $code
     * @return bool
     */
    public function matchesShortCode($code): bool
    {
        return Helper::convertX62ToDec($code) === $this->id;
    }

    /**
     * @param HttpRequest $request
     * @return string
     */
    public function getShortLink(HttpRequest $request): string
    {
        return sprintf('%s/%s', $request->getHostInfo(), $this->getShortCode());
    }

    /**
     * @param $url
     * @return bool
     */
    public function hasUrl($url): bool
    {
        return static::objects()->filter(['url' => Helper::trimUrl($url)])->count() > 0;
    }

    public function beforeSave(): void
    {
        $this->url = Helper::trimUrl($this->url);
    }

    /**
     * @param $url
     * @return ShortLink
     */
    public static function findByUrlOrCreate($url): Model
    {
        return static::objects()->filter(['url' => Helper::trimUrl($url)])->limit(1)->get()
            ?? new self(['url' => $url]);
    }

    public function toArray(HttpRequest $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'short_link' => $this->getShortLink($request),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}