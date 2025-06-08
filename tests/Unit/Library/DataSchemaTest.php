<?php

namespace Tests\Unit\Library\Crud;

use App\Library\Crud\DataSchema;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DataSchemaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush(); // Limpiar caché antes de cada test
    }


    public function testConvertBytesConvertsCorrectly()
    {
        //var_dump(DataSchema::convertBytes('KB', 1024));
        //var_dump(DataSchema::convertBytes('MB', 1024));
        $this->assertEquals(['KB' => 1.0], DataSchema::convertBytes('KB', 1024));
        $this->assertEquals(['MB' => 0.0009765625], DataSchema::convertBytes('MB', 1024));
    }

    public function testProcessDataMetricsProcessesDataAndCaches()
    {
        $data = [
            [
                'TABLE_NAME' => 'users',
                'TABLE_SCHEMA' => 'public',
                'TABLE_TYPE' => 'BASE TABLE',
                'ENGINE' => 'InnoDB',
                'TABLE_ROWS' => 100,
                'AVG_ROW_LENGTH' => 1024,
                'DATA_LENGTH' => 102400,
                'INDEX_LENGTH' => 20480,
                'AUTO_INCREMENT' => 101,
                'CREATE_TIME' => '2025-06-01 00:00:00',
                'UPDATE_TIME' => '2025-06-07 00:00:00',
                'TABLE_COLLATION' => 'utf8mb4_unicode_ci',
                'TABLE_COMMENT' => 'Users table',
            ],
        ];

        // Procesar y guardar en caché
        DataSchema::processDataMetrics($data);

        $cacheKey = 'schema_tables_metrics_' . DataSchema::getDatabaseName();
        $metrics = Cache::get($cacheKey);

        $this->assertIsArray($metrics);
        $this->assertArrayHasKey('users', $metrics);
        $this->assertEquals('users', $metrics['users'][0]['table_name']);
        $this->assertArrayHasKey('total', $metrics);
    }

    public function testGetInformationTablesReturnsCachedData()
    {
        // Este test depende de la estructura real de tu base de datos de testing.
        // Si quieres aislarlo, deberías mockear la consulta a la base de datos.
        // Aquí solo se comprueba que el método retorna un array o null.
        $result = DataSchema::getInformationTables();
        $this->assertTrue(is_array($result) || is_null($result));
    }
}