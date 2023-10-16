<?php
 
namespace App\Controllers\Api;
 
use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use \Firebase\JWT\JWT;
 
class AuthController extends BaseController
{
    use ResponseTrait;
 
    public function signup()
    {
        $rules = [
            'email' => ['rules' => 'required|min_length[4]|max_length[255]|valid_email|is_unique[users.email]'],
            'mobile' => ['rules' => 'required|min_length[10]|max_length[10]'],
            'password' => ['rules' => 'required|min_length[8]|max_length[255]'],
            'confirm_password'  => [ 'label' => 'confirm password', 'rules' => 'matches[password]']
        ];

        if($this->validate($rules)){
            $model = new UserModel();
            $data = [
                'email'    => $this->request->getVar('email'),
                'mobile'    => $this->request->getVar('mobile'),
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
            ];
            $model->save($data);
            return $this->respond(['message' => 'Registered Successfully'], 200);
        }else{
            $response = [
                'errors' => $this->validator->getErrors(),
                'message' => 'Invalid Inputs'
            ];
            return $this->fail($response , 409); 
        }
            
    }

    public function login(){
        $userModel = new UserModel();

        $rules = [
            'email' => ['rules' => 'required|min_length[4]|max_length[255]'],
            'password' => ['rules' => 'required|min_length[8]|max_length[255]'],
        ];

        if($this->validate($rules)){
            $email = $this->request->getVar('email');
            $password = $this->request->getVar('password');
              
            $user = $userModel->where('email', $email)->first();
      
            if(is_null($user)) {
                return $this->respond(['error' => 'Invalid username or password.'], 401);
            }
      
            $pwd_verify = password_verify($password, $user['password']);
      
            if(!$pwd_verify) {
                return $this->respond(['error' => 'Invalid username or password.'], 401);
            }
     
            $key = getenv('JWT_SECRET');
            $iat = time(); // current timestamp value
            $exp = $iat + 3600;
     
            $payload = array(
                "iss" => "Issuer of the JWT",
                "aud" => "Audience that the JWT",
                "sub" => "Subject of the JWT",
                "iat" => $iat, //Time the JWT issued at
                "exp" => $exp, // Expiration time of token
                "email" => $user['email'],
            );
             
            $token = JWT::encode($payload, $key, 'HS256');
     
            $response = [
                'message' => 'Login Succesful',
                'token' => $token
            ];
             
            return $this->respond($response, 200);
        }else{
            $response = [
                'errors' => $this->validator->getErrors(),
                'message' => 'Invalid Inputs'
            ];
            return $this->fail($response , 409); 
        }
    }
}