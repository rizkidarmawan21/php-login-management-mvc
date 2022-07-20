<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Controller;

use ProgrammerZamanNow\Belajar\PHP\MVC\App\View;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\SessionRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;
use ProgrammerZamanNow\Belajar\PHP\MVC\Service\sessionService;


class HomeController
{
    private sessionService $sessionService;

    public function __construct()
    {
        $connection = Database::getConnection();
        $sessionRepository = new SessionRepository($connection);
        $userRepository = new UserRepository($connection);
        $this->sessionService = new sessionService($sessionRepository, $userRepository);
    }

    public function index()
    {
        $user = $this->sessionService->current();

        if($user == null) {
            View::render('Home/index',[
                'title' => "PHP LOGIN MANAGEMENT"
            ]);
        } else {
            View::render('Home/dashboard',[
                'title' => "Dashboard",
                'user' => [
                    'name' => $user->name,
                ]
            ]);
        }


    }
}