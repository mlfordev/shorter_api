<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\HttpResponse;
use App\Lib\Helper;
use App\Models\ShortLink;
use App\Services\ShortLinkService;
use Exception;
use Phact\Exceptions\InvalidConfigException;
use Phact\Pagination\Pagination;

class ShortLinkController extends Controller
{
    /**
     * @return HttpResponse
     * @throws InvalidConfigException
     */
    public function index(): HttpResponse
    {
        $filter = [];
        $url = $this->request->getGet()->get('url');

        if (!empty($url)) {
            $filter['url'] = $url;
        }

        $qs = ShortLink::objects()->filter($filter)->order(['id']);

        $pager = new Pagination($qs, [
            'pageSize' => 10,
            'request' => $this->request,
        ]);

        /** @var ShortLink[]|null $links */
        $links = $pager->getData();

        if (!empty($links)) {
            $links = array_map(function ($item) {
                return $item->toArray($this->request);
            }, $links);
        }

        $linkHeader = $pager->getLinkHeader();

        if ($linkHeader !== '') {
            header($linkHeader);
        }

        return $this->json($links);
    }

    /**
     * @param int $id
     * @return HttpResponse
     */
    public function show(int $id): HttpResponse
    {
        /** @var ShortLink $model */
        $model = ShortLink::objects()->filter(['id' => $id])->limit(1)->get();

        if (is_null($model)) {
            return $this->json(['errors' => ['Ресурс не найден']], 404);
        }

        return $this->json($model->toArray($this->request));
    }

    /**
     * @return HttpResponse
     */
    public function create(): HttpResponse
    {
        $url = $this->request->getPost()->get('url');
        $url = Helper::trimUrl($url);

        if ($url === '') {
            return $this->json(['errors' => ['Укажите URL']], 422);
        }

        if (strpos($url, 'http') !== 0) {
            return $this->json(['errors' => ['Укажите протокол в URL']], 422);
        }

        try {
            if (!ShortLinkService::isUrlExists($url)) {
                return $this->json(['errors' => ['URL не доступен']], 422);
            }
        } catch (Exception $e) {
            return new HttpResponse(
                $e->getMessage() . PHP_EOL
                . $e->getLine() . ' line in ' . $e->getFile() . PHP_EOL
                . $e->getTraceAsString(),'Content-Type: text/plain; charset=UTF-8', 500
            );
        }

        $model = ShortLink::findByUrlOrCreate($url);

        if ($model->getIsNew()) {
            $id = $model->save();

            if (!$id) {
                return $this->json(['errors' => ['URL не сохранился в базе данных']], 500);
            }
        }

        return $this->json(['short_link' => $model->getShortLink($this->request)], 201);
    }

    /**
     * @param int $id
     * @return HttpResponse
     */
    public function update(int $id): HttpResponse
    {
        $url = $this->request->getPatch()->get('url');
        $url = Helper::trimUrl($url);

        if ($url === '') {
            return $this->json(['errors' => ['Укажите новый URL']], 422);
        }

        if (strpos($url, 'http') !== 0) {
            return $this->json(['errors' => ['Укажите протокол в URL']], 422);
        }

        if (ShortLink::objects()->filter(['url' => $url])->limit(1)->count() > 0) {
            return $this->json(['errors' => ['URL уже существует']], 422);
        }

        if (!ShortLinkService::isUrlExists($url)) {
            return $this->json(['errors' => ['URL не доступен']], 422);
        }

        /** @var ShortLink $model */
        $model = ShortLink::objects()->filter(['id' => $id])->limit(1)->get();

        if (is_null($model)) {
            return $this->json(['errors' => ['Ресурс не найден']], 404);
        }

        $model->url = $url;

        if (!$model->save()) {
            return $this->json(['errors' => ['URL не сохранился в базе данных']], 500);
        }

        return $this->json($model->toArray($this->request));
    }

    /**
     * @param int $id
     * @return HttpResponse
     */
    public function destroy(int $id): HttpResponse
    {
        ShortLink::objects()->filter(['id' => $id])->limit(1)->delete();
        return $this->json(null, 204);
    }
}