<?php
class DummyController extends Controller {
    public static bool $executed = false;
    public static ?string $param = null;

    public function testAction(string $id): void {
        self::$executed = true;
        self::$param = $id;
    }
}

function test_router_dispatching(): void {
    $router = new Router();
    $router->add('GET', '/test/:id', 'DummyController@testAction');

    DummyController::$executed = false;
    DummyController::$param = null;

    $router->dispatch('GET', '/test/42');

    assert_true(DummyController::$executed, 'Router should resolve and execute action');
    assert_equal('42', DummyController::$param, 'Router should pass URL parameters');
}
