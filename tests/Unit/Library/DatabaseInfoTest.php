<?php

namespace Tests\Unit\Library\Crud;

use App\Library\Crud\DatabaseInfo;
use Illuminate\Http\Request;
use Tests\TestCase;

class DatabaseInfoTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Asegurar que el contenedor de la aplicación esté limpio
        $this->app->instance('request', new Request());
    }

    public function testGetCurrentTableReturnsNullForInvalidTable()
    {
        $request = Request::create('/admin/grid/invalid', 'GET');
        $this->app->instance('request', $request);

        $dbInfo = new DatabaseInfo();
        $dbInfo->tableNames = ['users' => 'users']; // Solo 'users' es válido

        $currentTable = $dbInfo->getCurrentTable();

        $this->assertNull($currentTable);
    }

    public function testGetCurrentTableReturnsNullForEmptyRequest()
    {
        // Simular una solicitud sin segmentos
        $request = Request::create('/', 'GET');
        $this->app->instance('request', $request);

        $dbInfo = new DatabaseInfo();
        $currentTable = $dbInfo->getCurrentTable();

        $this->assertNull($currentTable);
    }

    public function testGetCurrentTableReturnsTableName()
    {
        $request = Request::create('/admin/grid/users', 'GET');
        $this->app->instance('request', $request);

        $dbInfo = new DatabaseInfo();
        $dbInfo->tableNames = ['users' => 'users']; // <-- Asegura que la tabla exista

        $currentTable = $dbInfo->getCurrentTable();

        $this->assertEquals('users', $currentTable);
    }
  
}