<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\HttpResponse;
use App\Models\Admin;
use Phact\Exceptions\InvalidConfigException;
use Phact\Pagination\Pagination;

/**
 * Class AdminController
 * @package App\Controllers
 */
class AdminController extends Controller
{
    private $passwordMinLength = 3;

    /**
     * @return HttpResponse
     * @throws InvalidConfigException
     */
    public function index(): HttpResponse
    {
        $filter = [];
        $login = $this->request->getGet()->get('login');

        if (!empty($login)) {
            $filter['login'] = $login;
        }

        $qs = Admin::objects()->filter($filter)->order(['id']);

        $pager = new Pagination($qs, [
            'pageSize' => 10,
            'request' => $this->request,
        ]);
        $pager->setDataType('raw');

        /** @var Admin[]|null $admins */
        $admins = $pager->getData();
        $admins = array_map(static function ($item) {
            unset($item['password']);
            return $item;
        }, $admins);

        $linkHeader = $pager->getLinkHeader();

        if ($linkHeader !== '') {
            header($linkHeader);
        }

        return $this->json($admins);
    }

    /**
     * @param int $id
     * @return HttpResponse
     */
    public function show(int $id): HttpResponse
    {
        /** @var Admin $model */
        $data = Admin::objects()->filter(['id' => $id])->limit(1)
            ->values(['id', 'login', 'created_at', 'updated_at']);

        if (is_null($data)) {
            return $this->json(['errors' => ['Ресурс не найден']], 404);
        }

        return $this->json($data[0]);
    }

    /**
     * @return HttpResponse
     */
    public function create(): HttpResponse
    {
        $login = $this->request->getPost()->get('login');
        $password = $this->request->getPost()->get('password');
        $data = [];

        if ($login === '') {
            $data['errors'][] = 'Поле Логин обязательно для заполнения';
        }

        if (Admin::objects()->filter(['login' => $login])->limit(1)->count() > 0) {
            $data['errors'][] = 'Администратор с таким логином уже существует';
        }

        if (!$password || mb_strlen($password) < $this->passwordMinLength) {
            $data['errors'][] = sprintf('Пароль должен быть длиннее %s символов', $this->passwordMinLength);
        }

        if (!empty($data['errors'])) {
            return $this->json($data, 422);
        }

        $model = new Admin();
        $model->login = $login;
        $model->password = password_hash($password,  PASSWORD_DEFAULT);
        $id = $model->save();

        if (!$id) {
            return $this->json(['errors' => ['Администратор не сохранился в базе данных']], 500);
        }

        return $this->json(['admin_id' => $id], 201);
    }

    /**
     * @param int $id
     * @return HttpResponse
     */
    public function update(int $id): HttpResponse
    {
        $login = $this->request->getServer()->get('PHP_AUTH_USER');
        $password = $this->request->getPatch()->get('password');
        $data = [];

        if (!$password || mb_strlen($password) < $this->passwordMinLength) {
            $data['errors'][] = sprintf('Пароль должен быть длиннее %s символов', $this->passwordMinLength);
        }

        if (!empty($data['errors'])) {
            return $this->json($data, 422);
        }

        $currentAdmin = Admin::getByLoginOrCreate($login);

        if ($currentAdmin->id !== $id) {
            return $this->json(['errors' => ['Вы не можете поменять пароль другому администратору']], 403);
        }

        $currentAdmin->password = password_hash($password,  PASSWORD_DEFAULT);

        if (!$currentAdmin->save()) {
            return $this->json(['errors' => ['Не получилось изменить пароль']], 500);
        }

        $data = $currentAdmin->getAttributes();
        unset($data['password']);

        return $this->json($data);
    }

    /**
     * @param int $id
     * @return HttpResponse
     */
    public function destroy(int $id): HttpResponse
    {
        $login = $this->request->getServer()->get('PHP_AUTH_USER');
        $currentAdmin = Admin::getByLoginOrCreate($login);

        if ($currentAdmin->id !== $id) {
            return $this->json(['errors' => ['Вы не можете удалить другого администратора']], 403);
        }

        Admin::objects()->filter(['id' => $id])->limit(1)->delete();
        return $this->json(null, 204);
    }
}