<?php

namespace Sasha\Slim;

use Exception;

class Repo
{
    private $repo;
    private const PATH = __DIR__ . '../users.json';

    //public function __construct()
    //{
    //    $file = file_get_contents(self::PATH, true);
    //    $this->repo = json_decode($file, true);
    //}

    // public function __construct()
    // {
    //     $file = file_get_contents(self::PATH, true);
    //     $this->repo = json_decode($file, true);
    // }
    
    // public function all($request) 
    // {
    //     return json_decode($request->getCookieParam('users', json_encode([])), true);
    // }

    // public function prepare($item, $request)
    // {

    //     if (empty($item['name']) || empty($item['email'])) {
    //         $json = json_encode($item);
    //         throw new Exception("Wrong user data {$json}");
    //     }

    //     if (!isset($item['id'])) {
    //         $item['id'] = uniqid();
    //     }

    //     $all = $this->all($request);
    //     $all[$item['id']] = $item;
    //     return json_encode($all);
    // }

    // public function find($id, $request)
    // {
    //     $all = $this->all($request);
    //     if (!isset($all[$id])) {
    //         throw new Exception('User not found');
    //     }
    //     return $all[$id];
    // }

    // public function delete($id, $request)
    // {
    //     $all = $this->all($request);
    //     if (!isset($all[$id])) {
    //         throw new \Exception('Wrond user id');
    //     }
    //     unset($all[$id]);
    //     return json_encode($all);
    // }
}