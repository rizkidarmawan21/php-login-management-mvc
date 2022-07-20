<?php

namespace ProgrammerZamanNow\Belajar\PHP\MVC\Service;

use PHPUnit\Framework\TestCase;
use ProgrammerZamanNow\Belajar\PHP\MVC\Config\Database;
use ProgrammerZamanNow\Belajar\PHP\MVC\Domain\User;
use ProgrammerZamanNow\Belajar\PHP\MVC\Excaption\ValidationException;
use ProgrammerZamanNow\Belajar\PHP\MVC\Model\UserRegisterRequest;
use ProgrammerZamanNow\Belajar\PHP\MVC\Repository\UserRepository;

class userServiceTest extends TestCase
{
    private userService $userService;
    private UserRepository $userRepository;
    protected function setUp(): void
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->userService = new userService($this->userRepository);

        $this->userRepository->deleteAll();
    }

    public function testRegisterSuccess() {
        $request = new UserRegisterRequest();
        $request->id = '1';
        $request->name = 'John';
        $request->password = '123';
        $response = $this->userService->register($request);
        $this->assertEquals($request->id, $response->user->id);
        $this->assertEquals($request->name, $response->user->name);
        $this->assertNotEquals($request->password, $response->user->password);

        self::assertTrue(password_verify($request->password, $response->user->password));
    }

    public function testRegisterFailed() {
        $this->expectException(ValidationException::class);
        $request = new UserRegisterRequest();
        $request->id = '';
        $request->name = '';
        $request->password = '';
        $this->userService->register($request);

    }
    public function testRegisterDuplicate() {
        $user = new User();
        $user->id = '1';
        $user->name = 'John';
        $user->password = '123';
        $this->userRepository->save($user);

        $this->expectException(ValidationException::class);

        $request = new UserRegisterRequest();
        $request->id = '1';
        $request->name = 'John';
        $request->password = '123';
        $this->userService->register($request);
    }


}
