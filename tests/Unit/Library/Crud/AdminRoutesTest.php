<?php

namespace Tests\Unit\Library\Crud;

use App\Library\Crud\AdminRoutes;
use App\Library\Crud\DataSchema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminRoutesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        //Route::enableFilters(); // Habilitar rutas para pruebas
        //DataSchema::DB_DATABASE = 'test_db';
    }

    public function testInitRoutesDefinesAdminRoutes()
    {
        $this->assertTrue(\Route::has('admin'));
    }

    public function testSetRoutesReturnsConfigOrTables()
    {
        // Simular config activa
        \Config::shouldReceive('get')->with('appweb.admin.active')->andReturn(true);
        \Config::shouldReceive('get')->with('appweb.admin.routes')->andReturn(['users' => ['title' => 'Users']]);
        $result = AdminRoutes::_setRoutes();
        $this->assertEquals(['users' => ['title' => 'Users']], $result);

        // Simular config inactiva
        \Config::shouldReceive('get')->with('appweb.admin.active')->andReturn(false);
        $mockDataSchema = $this->mock(DataSchema::class);
        $mockDataSchema->shouldReceive('getInformationTables')->andReturn(['users' => []]);
        $result = AdminRoutes::_setRoutes();
        //var_dump($result);
        $this->assertEquals(['users' => ['title' => 'Users']], $result);
    }
}