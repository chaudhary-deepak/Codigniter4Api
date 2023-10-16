<?php
 
namespace App\Controllers\Api;
 
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
 
class ApiController extends BaseController
{
    use ResponseTrait;
     
    public function users()
    {
        $users = new UserModel;
        return $this->respond(['users' => $users->findAll()], 200);
    }
}