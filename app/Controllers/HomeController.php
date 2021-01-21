<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\HttpResponse;
use App\Models\Click;
use App\Models\ShortLink;
use App\Services\ShortLinkService;

/**
 * Class HomeController
 *
 * @package App\Controllers
 */
class HomeController extends Controller
{
    /**
     * @return HttpResponse
     */
    public function index(): HttpResponse
    {
        return $this->render();
    }

    /**
     * @param $code
     * @return HttpResponse
     */
    public function redirectByCode($code): HttpResponse
    {
        $id = ShortLinkService::convertShortCodeToNumber($code);

        /** @var ShortLink $model */
        $shortLink = ShortLink::objects()->filter(['id' => $id])->limit(1)->get();

        if (!$shortLink) {
            return $this->notFound();
        }

        $server = $this->request->getServer();
        $click = new Click();
        $click->remote_addr = $server->get('REMOTE_ADDR') ;
        $click->remote_host = $server->get('REMOTE_HOST') ;
        $click->http_user_agent = $server->get('HTTP_USER_AGENT') ;
        $click->http_host = $server->get('HTTP_HOST') ;
        $click->http_referer = $server->get('HTTP_REFERER') ;
        $click->short_link = $shortLink;
        $click->save();

        return $this->redirect($shortLink->url);
    }
}