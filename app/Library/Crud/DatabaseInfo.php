<?php
namespace App\Library\Crud;

use App\Library\Crud\DataSchema;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class DatabaseInfo extends DataSchema
{
    protected $columnKeys = [];
    protected $columnPrimaryKeys = [];
    protected $allColumnTable = [];
    protected $allColumnTableEdit = [];
    protected $columnTypes = [];
    protected $dataTypes = [];
    protected $ordinalPositions = [];
    protected $isNullable = [];
    protected $columnComments = [];
    protected $tableNames = [];
    protected $maxLengths = [];
    protected $tableSchemaRelations = [];

    private const CACHE_TTL = 1440; // 24 horas en minutos

    public function __construct()
    {
        $this->getInformationSchema();
        $this->getInformationSchemaRelations();
    }

    /**
     * Obtiene la tabla actual desde la URL.
     *
     * @return string|null
     */
    protected function getCurrentTable(): ?string
    {
        $table = Request::segment(3);
        if ($table && array_key_exists($table, $this->tableNames)) {
            return $this->tableNames[$table];
        }
        return null;
    }

    /**
     * Obtiene la clave de columna para una tabla y tipo de clave específico.
     *
     * @param string $table
     * @param string $key
     * @return string|null
     */
    protected function getColumnKey(string $table, string $key): ?string
    {
        if (isset($this->columnKeys[$table])) {
            foreach ($this->columnKeys[$table] as $column => $value) {
                if ($value === $key) {
                    return $column;
                }
            }
        }
        return null;
    }

    /**
     * Obtiene y almacena las relaciones del esquema para la tabla actual.
     *
     * @return void
     */
    private function getInformationSchemaRelations(): void
    {
        $table = $this->getCurrentTable();
        if (!$table) {
            return;
        }

        $cacheKey = 'schema_relations_info_' . self::getDatabaseName() . '_' . $table;
        $result = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($table) {
            $data = self::getAllRelations($table);
            if (empty($data) || !is_array($data)) {
                return [];
            }

            $result = [];
            $referencedTables = [];
            foreach ($data as $row) {
                $referencedTables[$row['REFERENCED_TABLE_NAME']] = $row['UNIQUE_TABLE_NAME'];
            }

            foreach ($data as $index => $row) {
                $query = [];
                $uniqueTable = $row['UNIQUE_TABLE_NAME'];
                $query['unique_id'][$uniqueTable] = $this->getColumnKey($uniqueTable, 'PRI');
                $query['column_name_primary'][$uniqueTable] = $row['COLUMN_NAME'];
                $query['table_reference'][$row['REFERENCED_TABLE_NAME']] = $row['REFERENCED_COLUMN_NAME'];
                $query['column_reference'][$row['REFERENCED_TABLE_NAME']] = $this->getColumnKey($row['REFERENCED_TABLE_NAME'], 'UNI');

                if (count($referencedTables) > 1) {
                    foreach ($referencedTables as $refTable => $value) {
                        if ($mulKey = $this->getColumnKey($refTable, 'MUL')) {
                            $query['column_reference_id'][$refTable] = $mulKey;
                        }
                    }
                }

                $result[$uniqueTable][$index] = $query;
            }

            return $result;
        });

        $this->tableSchemaRelations = $result;
    }

    /**
     * Obtiene y almacena la información del esquema para la tabla actual.
     *
     * @return void
     */
    private function getInformationSchema(): void
    {
        try {
            if (Request::segment(1) !== 'admin' || !Request::segment(3)) {
                return;
            }

            $table = Request::segment(3);
            $tables = self::getInformationTables();
            if (!isset($tables[$table])) {
                return;
            }

            $cacheKey = 'schema_info_' . self::getDatabaseName() . '_' . $table;
            $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($table) {
                return self::getData($table);
            });

            if (empty($data) || !is_array($data)) {
                return;
            }

            foreach ($data as $tableName => $rows) {
                foreach ($rows as $row) {
                    $columnName = $row['COLUMN_NAME'];
                    $this->columnKeys[$tableName][$columnName] = $row['COLUMN_KEY'];
                    if ($row['COLUMN_KEY'] === 'PRI') {
                        $this->columnPrimaryKeys[$tableName][$columnName] = $row['COLUMN_KEY'];
                    }
                    $this->allColumnTable[$tableName][$columnName] = $columnName;
                    $this->allColumnTableEdit[$tableName][$columnName] = $row;
                    $this->columnTypes[$tableName][$columnName] = $row['COLUMN_TYPE'];
                    $this->dataTypes[$tableName][$columnName] = $row['DATA_TYPE'];
                    $this->ordinalPositions[$tableName][$columnName] = $row['ORDINAL_POSITION'];
                    $this->isNullable[$tableName][$columnName] = $row['IS_NULLABLE'];
                    $this->columnComments[$tableName][$columnName] = $row['COLUMN_COMMENT'];
                    $this->tableNames[$tableName] = $tableName;
                    $this->maxLengths[$tableName][$columnName] = $row['CHARACTER_MAXIMUM_LENGTH'] ?? null;
                }
            }
        } catch (\Exception $e) {
            self::handleException($e);
        }
    }
}