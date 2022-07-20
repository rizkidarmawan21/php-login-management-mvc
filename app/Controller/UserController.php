<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller;

use ProgrammerZamanNow\Belajar\PHP\MVC\App\View;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Excaption\ValidationException;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserLoginRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\sessionService;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\userService;
class UserController
{
    private userService $userService;
    private sessionService $sessionService;


    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new userService($userRepository);

        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new sessionService($sessionRepository , $userRepository);
    }

    public function register()
    {

        View::render('User/register',[
            'title' => 'Register',
        ]);
    }

    public function postRegister()
    {
        $request = new UserRegisterRequest();
        $request->id = $_POST['id'];
        $request->name = $_POST['name'];
        $request->password = $_POST['password'];


        try {
            $this->userService->register($request);
            View::redirect('/users/login');
        } catch (ValidationException $e) {
            View::render('User/register',[
                'title' => 'Register',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function login() {
        View::render('User/login',[
            'title' => 'Login user',
        ]);
    }

    public function postLogin() {
        $request = new UserLoginRequest();
        $request->id = $_POST['id'];
        $request->password = $_POST['password'];
        try {
            $response = $this->userService->login($request);
//            var_dump($response->user->id);
//            die;
            $this->sessionService->create($response->user->id);
            View::redirect('/');
        } catch (ValidationException $e) {
            View::render('User/login',[
                'title' => 'Login user',
                'error' => $e->getMessage()
            ]);
        }
    }
    public function logout()
    {
        $this->sessionService->destroy();
        View::redirect("/");
    }
}