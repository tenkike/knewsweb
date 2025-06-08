<?php

namespace Tests\Unit\Library\Crud;

use App\Library\Crud\DataTables;
use Tests\TestCase;
use ReflectionClass;

class DataTablesTest extends TestCase
{
    public function testSetEnumReturnsExpectedArray()
    {
        $dataTables = new DataTables();

        // Usar Reflection para acceder al método protegido
        $reflection = new ReflectionClass(DataTables::class);
        $method = $reflection->getMethod('setEnum');
        $method->setAccessible(true);

        $dataEnums = "'0','1'";
        $result = $method->invokeArgs($dataTables, [$dataEnums, '1']);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('1', $result);
        $this->assertEquals('On-line', $result['1']['enum']['name']);
        $this->assertTrue($result['1']['enum']['selected']);
        $this->assertEquals('bg-success', $result['1']['enum']['colorClass']);
    }

    public function testJoinClausesReturnsSchemaJoin()
    {
        $dataTables = new DataTables();

        $reflection = new \ReflectionClass(DataTables::class);
        $method = $reflection->getMethod('joinClauses');
        $method->setAccessible(true);

        $input = ['schemaJoin' => ['table1' => 'join1', 'table2' => 'join2']];
        $result = $method->invokeArgs($dataTables, [$input]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('table1', $result);
        $this->assertEquals('join1', $result['table1']);
    }

    public function testJoinSelectRelationReturnsArray()
    {
        $dataTables = new DataTables();

        $reflection = new \ReflectionClass(DataTables::class);
        $method = $reflection->getMethod('joinSelectRelation');
        $method->setAccessible(true);

        // Si no hay relación configurada, debe retornar un array vacío
        $result = $method->invoke($dataTables);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetDataDbReturnsArray()
    {
        $dataTables = new DataTables();

        $result = $dataTables->getDataDb();

        $this->assertIsArray($result);
    }
}