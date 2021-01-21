<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\HttpResponse;

/**
 * Class NotFoundController
 * @package App\Controllers
 */
class NotFoundController extends Controller
{
    /**
     * @return HttpResponse
     */
    public function index(): HttpResponse
    {
        return $this->notFound();
    }
}