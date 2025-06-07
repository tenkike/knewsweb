<?php
namespace App\Library\Crud;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DataSchema
{
    private const DB_DATABASE = 'carthome';
	private const CACHE_TTL = 1440; // 24 horas en minutos
    public static $schemaDataTable = [];

    protected function __construct()
    {
        // Evita la instanciación
    }

	/**
     * Obtiene el nombre de la base de datos.
     *
     * @return string
     */
    public static function getDatabaseName(): string
    {
        return self::DB_DATABASE;
    }
	
    /**
     * Obtiene datos del esquema para una tabla específica.
     *
     * @param string $table
     * @return array|null
     */
    public static function getData(string $table): ?array
    {
        $tables = self::getInformationTables();
        if (!array_key_exists($table, $tables)) {
            return null;
        }

        $data = self::querySchemaColumns($table);
        if (empty($data)) {
            return null;
        }

        $result = [];
        foreach ($data as $row) {
            if (isset($row['TABLE_NAME'])) {
                $result[$row['TABLE_NAME']][] = $row;
            }
        }

        return $result;
    }

    /**
     * Obtiene todas las relaciones de una tabla específica.
     *
     * @param string $table
     * @return array|null
     */
    public static function getAllRelations(string $table): ?array
    {
        $tables = self::getInformationTables();
        if (!array_key_exists($table, $tables)) {
            return null;
        }

        $data = self::querySchemaRelationsTables($table);
        if (empty($data)) {
            return null;
        }

        $result = [];
        foreach ($data as $index => $row) {
            if ($tables[$table] === $row['UNIQUE_TABLE_NAME']) {
                $result[$index] = $row;
            }
        }

        return $result;
    }

    /**
     * Convierte bytes a la unidad especificada (KB, MB, GB).
     *
     * @param string $unit
     * @param int $bytes
     * @return array
     */
    private static function convertBytes(string $unit, int $bytes): array
    {
        $result = [];
        switch (strtoupper($unit)) {
            case 'KB':
                $result['kb'] = $bytes / 1024;
                break;
            case 'MB':
                $result['mb'] = $bytes / (1024 * 1024);
                break;
            case 'GB':
                $result['gb'] = $bytes / (1024 * 1024 * 1024);
                break;
        }
        return $result;
    }

    /**
     * Procesa los datos del esquema para las tablas.
     *
     * @param array $data
     * @return void
     */
    private static function processSchemaDataTable(array $data): void
    {
        $result = [];
        $totalAvg = $totalDataLength = $totalIndexLength = 0;
        if (empty($data)) {
            Log::error("Error en processSchemaDataTable: data está vacío". json_encode($data));
            return;
        }

        foreach ($data as $row) {
            $tableName = $row['TABLE_NAME'];
            $result[$tableName][] = [
                'table_schema' => $row['TABLE_SCHEMA'],
                'table_name' => $row['TABLE_NAME'],
                'table_type' => $row['TABLE_TYPE'],
                'engine' => $row['ENGINE'],
                'table_rows' => $row['TABLE_ROWS'],
                'avg_row_length' => self::convertBytes('KB', $row['AVG_ROW_LENGTH']),
                'data_length' => self::convertBytes('KB', $row['DATA_LENGTH']),
                'index_length' => self::convertBytes('KB', $row['INDEX_LENGTH']),
                'auto_increment' => $row['AUTO_INCREMENT'],
                'create_time' => $row['CREATE_TIME'],
                'update_time' => $row['UPDATE_TIME'],
                'table_collation' => $row['TABLE_COLLATION'],
                'table_comment' => $row['TABLE_COMMENT'],
            ];

            $totalAvg += $row['AVG_ROW_LENGTH'];
            $totalDataLength += $row['DATA_LENGTH'];
            $totalIndexLength += $row['INDEX_LENGTH'];
        }

        $result['total'] = [
            'total_avg_row_length' => self::convertBytes('KB', $totalAvg),
            'total_data_length' => self::convertBytes('KB', $totalDataLength),
            'total_index_length' => self::convertBytes('KB', $totalIndexLength),
        ];

        self::$schemaDataTable = json_encode($result);
    }

    /**
     * Obtiene todas las tablas de la base de datos con caché.
     *
     * @return array|null
     */
    public static function getInformationTables(): ?array
    {
        $cacheKey = 'schema_tables_' . self::DB_DATABASE;

        Log::info('Llamada a getInformationTables');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            try {
                $data = self::querySchemaTables();

                if (empty($data)) {
                    Log::error("Error en getInformationTables: data está vacío");
                    return null;
                }

                $result = [];
                foreach ($data as $row) {
                    $result[$row['TABLE_NAME']] = $row['TABLE_NAME'];
                }

                self::processSchemaDataTable($data);
                return $result;
            } catch (\Exception $e) {
                self::handleException($e);
                return null;
            }
        });
    }


    /**
     * Maneja excepciones y registra detalles de error.
     *
     * @param \Exception $e
     * @return void
     */
    private static function handleException(\Exception $e): void
    {
        Log::error("Error en DataSchema: Código: {$e->getCode()}, Mensaje: {$e->getMessage()}, Línea: {$e->getLine()}");
    }

    /**
     * Ejecuta una consulta SQL cruda.
     *
     * @param string $query
     * @param array $bindings
     * @return array
     */
    private static function executeRawQuery(string $query, array $bindings = []): array
    {
        try {
            return DB::select(DB::raw($query)->getValue(DB::connection()->getQueryGrammar()), $bindings);
        } catch (\Exception $e) {
            self::handleException($e);
            return [];
        }
    }

    /**
     * Convierte resultados de consultas a un arreglo.
     *
     * @param array $data
     * @return array
     */
    private static function toArray(array $data): array
    {
        $results = [];
        foreach ($data as $index => $row) {
            if (is_object($row)) {
                $results[$index] = get_object_vars($row);
            }
        }
        return $results;
    }

    /**
     * Consulta las tablas del esquema con caché.
     *
     * @return array
     */
    private static function querySchemaTables(): array
    {
        $cacheKey = 'schema_tables_data_' . self::DB_DATABASE;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            $query = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?";
            return self::toArray(self::executeRawQuery($query, [self::DB_DATABASE]));
        });
    }

    /**
     * Consulta las columnas del esquema para una tabla específica con caché.
     *
     * @param string $table
     * @return array
     */
    private static function querySchemaColumns(string $table): array
    {
        $cacheKey = 'schema_columns_' . self::DB_DATABASE . '_' . $table;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($table) {
            $query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION";
            return self::toArray(self::executeRawQuery($query, [self::DB_DATABASE, $table]));
        });
    }

    /**
     * Consulta las relaciones del esquema para una tabla específica con caché.
     *
     * @param string $table
     * @return array
     */
    private static function querySchemaRelationsTables(string $table): array
    {
        $cacheKey = 'schema_relations_' . self::DB_DATABASE . '_' . $table;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($table) {
            $query = "
                SELECT K.TABLE_NAME AS UNIQUE_TABLE_NAME,
                       K.COLUMN_NAME,
                       K.UNIQUE_CONSTRAINT_SCHEMA,
                       K.UNIQUE_CONSTRAINT_NAME,
                       K.REFERENCED_TABLE_NAME,
                       K.REFERENCED_COLUMN_NAME,
                       K.CONSTRAINT_NAME AS FOREING_CONSTRAINT_NAME,
                       K.ORDINAL_POSITION,
                       K.POSITION_IN_UNIQUE_CONSTRAINT,
                       K.CONSTRAINT_SCHEMA
                FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS RC
                INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE K
                    ON K.CONSTRAINT_CATALOG = RC.CONSTRAINT_CATALOG
                    AND K.CONSTRAINT_SCHEMA = RC.CONSTRAINT_SCHEMA
                    AND K.CONSTRAINT_NAME = RC.CONSTRAINT_NAME
                    AND K.TABLE_NAME = RC.TABLE_NAME
                    AND K.REFERENCED_TABLE_NAME = RC.REFERENCED_TABLE_NAME
                WHERE K.CONSTRAINT_SCHEMA = ? AND K.TABLE_NAME = ?
                ORDER BY K.CONSTRAINT_NAME
            ";
            return self::toArray(self::executeRawQuery($query, [self::DB_DATABASE, $table]));
        });
    }
}