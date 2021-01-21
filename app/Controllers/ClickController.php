<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\HttpResponse;
use App\Models\Click;
use App\Services\ShortLinkService;
use Phact\Exceptions\InvalidConfigException;
use Phact\Pagination\Pagination;

/**
 * Class ClickController
 * @package App\Controllers
 */
class ClickController extends Controller
{
    /**
     * @return HttpResponse
     * @throws InvalidConfigException
     */
    public function index(): HttpResponse
    {
        $filter = [];
        $shortCode = $this->request->getGet()->get('short_code');

        if (!empty($shortCode)) {
            $shortLinkId = ShortLinkService::convertShortCodeToNumber($shortCode);
            $filter['short_link_id'] = $shortLinkId;
        }

        $qs = Click::objects()->filter($filter)->with(['short_links'])->order(['id']);

        $pager = new Pagination($qs, [
            'pageSize' => 10,
            'request' => $this->request,
        ]);

        /** @var Click[]|null $links */
        $clicks = $pager->getData();

        $linkHeader = $pager->getLinkHeader();

        if ($linkHeader !== '') {
            header($linkHeader);
        }

        if (!empty($clicks)) {
            $clicks = array_map(function ($item) {
                /** @var Click $item */
                $attributes = $item->getAttributes();
                $attributes['short_link'] = $item->short_link->getShortLink($this->request);
                return $attributes;
            }, $clicks);
        }

        return $this->json($clicks);
    }
}