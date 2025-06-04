<?php
namespace App\Library\Crud;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class DataTables extends DatabaseInfo
{
    protected $dataDb = [];
    protected $dataInputs = [];
    protected $createInputs = [];
    protected $editInputs = [];
    protected $gridHtml=[];
    protected $totalPages = 10; // Número total de páginas para paginación
    public $formHtmlCreate;
    public $formHtmlUpdate;
    public $arrayAddActions = [];
    public $getColsNames = [];

    private const CACHE_TTL = 1440; // 24 horas en minutos

    public function __construct()
    {
        parent::__construct();
        $this->initialize();
    }

    /**
     * Inicializa la clase cargando datos si hay una tabla válida.
     *
     * @return void
     */
    private function initialize(): void
    {
        if ($this->getCurrentTable()) {
            $this->fetchDataDb();
            $this->addActions();
        }
    }

    /**
     * Crea un paginador para los datos proporcionados.
     *
     * @param array $data
     * @return LengthAwarePaginator
     */
    private function createPaginator(array $data): LengthAwarePaginator
    {
        $collection = collect($data);
        $perPage = $this->totalPages;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $collection->forPage($currentPage, $perPage),
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    /**
     * Renderiza la cuadrícula de datos para la tabla actual.
     *
     * @param array $array
     * @return self
     */
    public function renderGrid(array $array = []): array
{
    try {
        $tableName = Request::segment(3);
        Log::info("Renderizando grilla para tabla: $tableName", ['params' => Request::all()]);

        if (Request::segment(2) !== 'grid') {
            Log::warning('Ruta inválida: ' . Request::fullUrl());
            return [
                'error' => 'Ruta inválida',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'draw' => (int) Request::input('draw', 1),
            ];
        }

        if (!$this->validateTable($tableName)) {
            Log::warning("Tabla no encontrada: $tableName");
            return [
                'error' => 'Tabla no encontrada',
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'draw' => (int) Request::input('draw', 1),
            ];
        }

        $this->fetchDataDb();
        return $this->gridHtml;
    } catch (\Exception $e) {
        Log::error("Error en renderGrid para tabla $tableName: " . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'params' => Request::all(),
        ]);
        return [
            'error' => 'Error en el servidor',
            'message' => $e->getMessage(),
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'draw' => (int) Request::input('draw', 1),
        ];
    }
}

    /**
     * Genera el formulario HTML para crear registros.
     *
     * @return array|null
     */
    public function formHtmlCreate(): ?array
    {
        if (Request::segment(4) === 'create') {
            $this->generateInputsHtml();
            return $this->createInputs;
        }
        return null;
    }

    /**
     * Genera el formulario HTML para actualizar registros.
     *
     * @return array|null
     */
    public function formHtmlUpdate(): ?array
    {
        if (Request::segment(4) === 'update') {
            $this->generateInputsHtml();
            return $this->editInputs;
        }
        return null;
    }

    /**
     * Genera los inputs HTML para formularios de creación o actualización.
     *
     * @return void
     */
    private function generateInputsHtml(): void
    {
        if (Request::segment(2) !== 'form') {
            return;
        }

        $tableName = Request::segment(3);
        if (!$this->validateTable($tableName)) {
            return;
        }

        $cacheKey = 'form_config_' . self::getDatabaseName() . '_' . $tableName;
        $configForms = Cache::remember($cacheKey, self::CACHE_TTL, fn() => $this->buildInputForms());

        $action = Request::segment(4);
        if ($action === 'create') {
            $this->createInputs = $this->loadInputsHtml([
                'config' => $configForms[$tableName] ?? [],
                'id' => '',
            ]);
        } elseif ($action === 'update' && Request::segment(5)) {
            $this->editInputs = $this->loadInputsHtml([
                'config' => $configForms[$tableName] ?? [],
                'id' => Request::segment(5),
                'data' => $this->dataDb[$tableName] ?? [],
            ]);
        }
    }

    /**
     * Obtiene la posición de los padres para un campo id_parent.
     *
     * @return array|null
     */
    private function getPositionIdParent(): ?array
    {
        $tableName = $this->getCurrentTable();
        return $tableName ? $this->processSelectParentId($tableName, 'position') : null;
    }

    /**
     * Realiza una búsqueda con expresiones regulares.
     *
     * @param string $regex
     * @param string $type
     * @return array
     */
    private function pregMatchAll(string $regex, string $type): array
    {
        preg_match_all($regex, $type, $matches);
        return $matches;
    }

    /**
     * Configura opciones para un campo enum.
     *
     * @param string $dataEnums
     * @param string|int $value
     * @return array
     */
    protected function setEnum($dataEnums, $value = 0): array
    {
        $selectEnum = [];
        $enumOptions = [
            '0' => ['name' => 'Off-line'],
            '1' => ['name' => 'On-line'],
        ];
        $enumColorClasses = [
            '0' => 'bg-danger',
            '1' => 'bg-success',
        ];

        $matches = $this->pregMatchAll("/'(.*?)'/", $dataEnums);
        foreach ($matches[1] as $selVal) {
            if (isset($enumOptions[$selVal])) {
                $selectEnum[$selVal]['enum'] = $enumOptions[$selVal];
                $selectEnum[$selVal]['enum']['colorClass'] = $enumColorClasses[$selVal];
                if ($value === $selVal) {
                    $selectEnum[$selVal]['enum']['selected'] = true;
                }
            }
        }

        return $selectEnum;
    }

    /**
     * Agrega acciones personalizadas desde la configuración.
     *
     * @return self
     */
    protected function addActions(): self
    {
        $this->arrayAddActions = Config::get('appweb.admin.grid.actions', []);
        return $this;
    }

    /**
     * Procesa las opciones para un campo id_parent.
     *
     * @param string $tableName
     * @param string $columnName
     * @param int $idParent
     * @return array|null
     */
    protected function processSelectParentId(string $tableName, string $columnName, int $idParent = 0): ?array
    {
        if (!isset($this->allColumnTable[$tableName][$columnName])) {
            return null;
        }

        $options = [];
        $colId = $this->getColumnKey($tableName, 'PRI');
        $colUni = $this->getColumnKey($tableName, 'UNI');
        $data = $this->dataDb[$tableName] ?? [];

        foreach ($data as $value) {
            $options[$value->$colId] = ($idParent === (int)$value->$columnName)
                ? [$value->$colId => $value->$colUni]
                : [$value->$columnName => $value->$colUni];
        }

        return $options;
    }

    /**
     * Carga el HTML para la cuadrícula de datos.
     *
     * @param array $data
     * @return array
     */
    protected function loadGridHtml(array $data): array
    {
        try {
            if (empty($data['data']) || !isset($data['route'])) {
                return ['error' => 'Datos inválidos'];
            }

            $tableName = $data['route'];
            if (!isset($this->allColumnTable[$tableName])) {
                return ['error' => 'Tabla no encontrada'];
            }

            $items = $data['data'];
            $arrayGrid = [];
            $dataRows = [];
            $fetchOptions = $this->fetchOptions($items->items());

            $idIndex = $this->getColumnKey($tableName, 'PRI');
            $tableTitle = str_replace('vk_', '', $tableName);
            $arrayGrid['table-title'] = $tableName;
            $arrayGrid['name-title'] = Config::get('appweb.admin.grid.title', '') . "-$tableTitle";
            $arrayGrid['create'] = 'create_' . $tableName;
            if (Str::startsWith($tableName, 'vk_')) {
                $arrayGrid['pdf'] = 'pdf_print_' . $tableName;
            }

            $ordinalPositions = $this->ordinalPositions[$tableName] ?? [];

            foreach ($items->items() as $k => $row) {
                foreach ($row as $j => $value) {
                    if (!empty($data['array']) && isset($data['array'][$j])) {
                        $arrayGrid['cols'][$j] = $data['array'][$j];
                        $arrayGrid['rows'][$k][$j] = $row->$j;
                    } elseif (empty($data['array'])) {
                        $arrayGrid['cols'][$j] = $j;
                        $arrayGrid['rows'][$k][$j] = isset($fetchOptions[$j]) ? $fetchOptions[$j] : $row->$j;
                    }

                    if (isset($this->dataTypes[$tableName][$j]) && $this->dataTypes[$tableName][$j] === 'enum') {
                        $colEnum = $this->allColumnTable[$tableName][$j];
                        $dataEnums = $this->columnTypes[$tableName][$j];
                        $selectEnums[$row->$idIndex] = $this->setEnum($dataEnums, $row->$colEnum);
                        $arrayGrid['rows'][$k][$j] = $selectEnums;
                    }

                    if (isset($this->columnKeys[$tableName][$j]) && $this->columnKeys[$tableName][$j] === 'PRI') {
                        $colId = $this->allColumnTable[$tableName][$j];
                        $actions = !empty($this->arrayAddActions) ? $this->arrayAddActions : [
                            'update' => "/admin/form/$tableName/update/{$row->$colId}",
                            'delete' => "/admin/$tableName/delete/{$row->$colId}",
                            'images' => "/admin/$tableName/images/{$row->$colId}",
                        ];
                        $arrayGrid['rows'][$k]['actions'] = $actions;
                    }

                    if (isset($ordinalPositions[$j])) {
                        $arrayGrid['positions'][$j] = $ordinalPositions[$j];
                        $dataRows['rows'][$k][$j] = $arrayGrid['rows'][$k][$j];
                    }
                }

                $arrayGrid['positions']['actions'] = end($ordinalPositions) + 1;
                $dataRows['rows'][$k]['actions'] = $arrayGrid['rows'][$k]['actions'];
                $arrayGrid['cols']['actions'] = 'actions';
            }

            return [
                'cols' => $arrayGrid['cols'] ?? [],
                'rows' => $dataRows['rows'] ?? [],
                'positions' => $arrayGrid['positions'] ?? [],
                'table-title' => $arrayGrid['table-title'],
                'name-title' => $arrayGrid['name-title'],
                'create' => $arrayGrid['create'] ?? [],
                'pdf' => $arrayGrid['pdf'] ?? [],
            ];
        } catch (\Exception $e) {
            \Log::error("Error en loadGridHtml: {$e->getMessage()}");
            return ['error' => 'Error al cargar la cuadrícula'];
        }
    }

    /**
     * Carga el HTML para los inputs de formularios.
     *
     * @param array $data
     * @return array|string
     */
    protected function loadInputsHtml(array $data)
    {
        if (!isset($data['config'])) {
            return ['error' => 'Configuración requerida'];
        }

        $tableName = Request::segment(3);
        $idIndex = $this->getColumnKey($tableName, 'PRI');
        $fetchOptions = $this->fetchOptions();
        $outInputs = [];
        $attributes = [];
        $outInputs['actions']['back'] = route('grid_' . $tableName);

        foreach ($data['config'] as $k => $row) {
            $outInputs[$k] = [
                'type' => $row['type'],
                'id' => $row['id'],
                'name' => $row['name'],
                'attributes' => [
                    'readonly' => $this->dataTypes[$tableName][$k] === 'timestamp',
                    'disabled' => $this->columnKeys[$tableName][$k] === 'PRI',
                    'required' => $this->isNullable[$tableName][$k] === 'NO',
                    'maxlength' => $this->maxLengths[$tableName][$k] ?: false,
                ],
            ];

            if ($row['type'] === 'number' && $k !== $this->allColumnTable[$tableName][$idIndex]) {
                $max = ($this->allColumnTable[$tableName][$k] === 'position') ? 1 : ($row['max'] ?? 10000);
                $outInputs[$k]['attributes'] += [
                    'step' => $row['step'] ?? 1,
                    'min' => $row['min'] ?? 0,
                    'max' => $max,
                ];
            }

            if (empty($data['id'])) {
                if ($this->dataTypes[$tableName][$k] === 'enum') {
                    $dataEnums = $this->columnTypes[$tableName][$k];
                    $outInputs[$k]['value'] = ['none' => [0 => $this->setEnum($dataEnums)]];
                } elseif ($this->allColumnTable[$tableName][$k] === 'id_parent') {
                    $outInputs[$k]['value'] = $this->processSelectParentId($tableName, 'id_parent');
                } elseif (isset($fetchOptions[$k])) {
                    $outInputs[$k]['value'] = $fetchOptions[$k];
                } else {
                    $outInputs[$k]['value'] = $row['value'] ?? '';
                }
                $outInputs['actions']['create'] = route("create.insert_$tableName");
                return ['create' => $outInputs];
            }

            if (isset($data['data'])) {
                foreach ($data['data'] as $dbRow) {
                    if ((int)$data['id'] === (int)($dbRow->$idIndex ?? 0)) {
                        $outInputs['actions']['update'] = route("update.insert_$tableName", $dbRow->$idIndex);
                        if ($this->dataTypes[$tableName][$k] === 'enum') {
                            $colEnum = $this->allColumnTable[$tableName][$k];
                            $dataEnums = $this->columnTypes[$tableName][$k];
                            $outInputs[$k]['value'] = [$dbRow->$idIndex => [$dbRow->$colEnum => $this->setEnum($dataEnums, $dbRow->$colEnum)]];
                        } elseif ($this->allColumnTable[$tableName][$k] === 'id_parent') {
                            $outInputs[$k]['value'] = $this->processSelectParentId($tableName, 'id_parent', $dbRow->id_parent ?? 0);
                        } elseif (isset($fetchOptions[$k])) {
                            $outInputs[$k]['value'] = $fetchOptions[$k];
                        } else {
                            $outInputs[$k]['value'] = $dbRow->$k ?? '';
                        }
                    }
                }
                return ['update' => $outInputs];
            }
        }

        return ['error' => 'Datos inválidos'];
    }

    /**
     * Obtiene los datos de la base de datos.
     *
     * @return array
     */
    protected function getDataDb(): array
    {
        return $this->dataDb;
    }

    /**
     * Establece los datos de la base de datos.
     *
     * @param array $data
     * @return void
     */
    private function setDataDb(array $data): void
    {
        $this->dataDb = $data;
    }

    /**
     * Carga los datos de la base de datos para la tabla actual.
     * fetchdatadb
     * @return void
     */
        private function fetchDataDb(): void
{
    $tableName = $this->getCurrentTable();
    if (!$tableName) {
        Log::warning('No se proporcionó nombre de tabla en la URL', ['url' => Request::fullUrl()]);
        $this->gridHtml = [
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'draw' => (int) Request::input('draw', 1),
        ];
        return;
    }

    if (!$this->validateTable($tableName)) {
        Log::warning("Tabla no encontrada: $tableName", ['available_tables' => array_keys($this->allTables)]);
        $this->gridHtml = [
            'error' => 'Tabla no encontrada',
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'draw' => (int) Request::input('draw', 1),
        ];
        return;
    }

    $cacheKey = 'data_db_' . self::getDatabaseName() . '_' . $tableName . '_' . md5(json_encode(Request::all()));
    $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($tableName) {
        try {
            $query = DB::table($tableName);
            $id = $this->getColumnKey($tableName, 'PRI') ?? 'id';

            // Log de parámetros recibidos
            Log::debug("Procesando consulta para tabla: $tableName", [
                'start' => Request::input('start', 0),
                'length' => Request::input('length', 10),
                'search' => Request::input('search.value'),
                'order_column_index' => Request::input('order.0.column'),
                'order_column' => Request::input('columns.' . Request::input('order.0.column') . '.data'),
                'order_dir' => Request::input('order.0.dir', 'asc'),
            ]);

            // Ordenación
            $orderColumnIndex = Request::input('order.0.column');
            if ($orderColumnIndex !== null) {
                $orderColumn = Request::input("columns.$orderColumnIndex.data");
                $orderDir = Request::input('order.0.dir', 'asc');
                if ($orderColumn && Schema::hasColumn($tableName, $orderColumn)) {
                    $query->orderBy($orderColumn, $orderDir);
                } else {
                    Log::warning("Columna de ordenación inválida: $orderColumn", ['table' => $tableName]);
                }
            }
            if (!$orderColumnIndex && $id && Schema::hasColumn($tableName, $id)) {
                $query->orderBy("$tableName.$id", 'asc');
            }

            // Búsqueda
            if ($search = Request::input('search.value')) {
                $columns = $this->allColumnTable[$tableName] ?? Schema::getColumnListing($tableName);
                if (!empty($columns)) {
                    $query->where(function ($q) use ($tableName, $search, $columns) {
                        foreach ($columns as $column) {
                            if (Schema::hasColumn($tableName, $column)) {
                                $q->orWhere("$tableName.$column", 'LIKE', "%$search%");
                            }
                        }
                    });
                } else {
                    Log::warning("No se encontraron columnas para búsqueda en tabla: $tableName");
                }
            }

            // Paginación
            $start = (int) Request::input('start', 0);
            $length = (int) Request::input('length', 10);

            // Log de la consulta SQL
            $sql = $query->toSql();
            $bindings = $query->getBindings();
            Log::debug("Consulta SQL: $sql", ['bindings' => $bindings]);

            $totalRecords = DB::table($tableName)->count();
            $filteredRecords = $search ? $query->count() : $totalRecords;

            // Obtener filas
            $rows = $query->skip($start)->take($length)->get()->map(function ($row) use ($tableName) {
                $rowArray = (array) $row;
                // Verificar que 'id' exista antes de usarlo
                $rowId = isset($row->id) ? $row->id : (isset($rowArray[$this->getColumnKey($tableName, 'PRI')]) ? $rowArray[$this->getColumnKey($tableName, 'PRI')] : null);
                if ($rowId === null) {
                    Log::warning("Fila sin ID en tabla: $tableName", ['row' => $rowArray]);
                }
                $rowArray['actions'] = [
                    'update' => $rowId ? "/admin/form/$tableName/update/$rowId" : '#',
                    'delete' => $rowId ? "/admin/$tableName/delete/$rowId" : '#',
                    'images' => $rowId ? "/admin/$tableName/images/$rowId" : '#',
                ];
                return $rowArray;
            })->toArray();

            Log::debug("Filas obtenidas: " . count($rows), ['rows' => $rows]);

            return [
                'data' => $rows,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
            ];
        } catch (\Exception $e) {
            Log::error("Error al consultar tabla $tableName: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'params' => Request::all(),
            ]);
            return [
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
            ];
        }
    });

    $this->gridHtml = [
        'data' => $data['data'],
        'recordsTotal' => $data['recordsTotal'],
        'recordsFiltered' => $data['recordsFiltered'],
        'draw' => (int) Request::input('draw', 1),
    ];
}

    /**
     * Obtiene opciones para campos relacionados.
     *
     * @param array $find
     * @return array
     */
    private function fetchOptions(array $find = []): array
    {
        $relations = $this->tableSchemaRelations;
        $joinSelectRelation = $this->joinSelectRelation();
        $options = [];

        foreach ($relations as $table => $data) {
            foreach ($data as $row) {
                $uniqueId = $this->sqlColumn($row['unique_id'])['rows'] ?? null;
                $tableReference = $this->sqlColumn($row['table_reference'])['key'] ?? null;
                $tableReferenceId = ($this->sqlColumn($row['table_reference'])['rows'] ?? '') . '_' . $tableReference;
                $columnNamePrimary = $this->sqlColumn($row['column_name_primary'])['rows'] ?? null;
                $columnReference = $this->sqlColumn($row['column_reference'])['rows'] ?? null;

                $dataDb = $this->dataDb[$tableReference] ?? [];
                $joinRows = $joinSelectRelation[$tableReference] ?? [];

                foreach ($dataDb as $dbRow) {
                    if ($find) {
                        foreach ($find as $findDb) {
                            foreach ($joinRows as $join) {
                                if ($join->$uniqueId === $findDb->$uniqueId &&
                                    $findDb->$columnNamePrimary === $join->$columnNamePrimary) {
                                    $key = $join->id_category ?? $join->$columnNamePrimary;
                                    $options[$columnNamePrimary][$findDb->$uniqueId][$key] = $join->$columnReference;
                                }
                            }
                        }
                    }

                    $columnReferenceId = isset($row['column_reference_id']) ? $this->sqlColumn($row['column_reference_id']) : [];
                    $idCategory = $columnReferenceId['rows'] ?? null;

                    if (isset($dbRow->$idCategory)) {
                        $options[$columnNamePrimary][$dbRow->$idCategory][$dbRow->$tableReferenceId] = $dbRow->$columnReference;
                    } else {
                        $options[$columnNamePrimary][$dbRow->$tableReferenceId] = $dbRow->$columnReference;
                    }
                }
            }
        }

        return $options;
    }

    /**
     * Procesa cláusulas de unión para consultas.
     *
     * @param array $data
     * @return array
     */
    private function joinClauses(array $data): array
    {
        return $data['schemaJoin'] ?? [];
    }

    /**
     * Realiza consultas con uniones para relaciones.
     *
     * @return array
     */
    private function joinSelectRelation(): array
    {
        $relationData = $this->getSqlRelationTables();
        if (empty($relationData['table'])) {
            return [];
        }

        $cacheKey = 'join_relation_' . self::getDatabaseName() . '_' . $relationData['table'];
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($relationData) {
            $query = DB::table($relationData['table'])->selectRaw($relationData['selects']);
            $joinClauses = $this->joinClauses($relationData);

            foreach ($joinClauses as $table => $conditions) {
                $query->join($table, key($conditions), '=', current($conditions));
            }

            $query->groupBy($relationData['unique_id'])->orderBy($relationData['unique_id'], 'asc');
            $data = [];
            foreach ($joinClauses as $table => $conditions) {
                $data[$table] = $query->get();
            }

            return $data;
        });
    }

    /**
     * Extrae información de una columna SQL.
     *
     * @param array $array
     * @return array
     */
    private function sqlColumn(array $array): array
    {
        try {
            foreach ($array as $key => $value) {
                return ['key' => $key, 'rows' => $value];
            }
            return [];
        } catch (\Exception $e) {
            \Log::error("Error en sqlColumn: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Obtiene las relaciones SQL para la tabla actual.
     *
     * @return array
     */
    private function getSqlRelationTables(): array
    {
        $table = $this->getCurrentTable();
        if (!$table || !isset($this->tableSchemaRelations[$table])) {
            return ['table' => ''];
        }

        $array = $this->tableSchemaRelations[$table];
        $selectSql = ['table' => $table];
        $selectQuery = [];

        foreach ($array as $row) {
            $tableReference = $this->sqlColumn($row['table_reference'])['key'] ?? null;
            $uniqueId = $this->sqlColumn($row['unique_id'])['rows'] ?? null;
            $columnNamePrimary = $this->sqlColumn($row['column_name_primary'])['rows'] ?? null;
            $columnIdReference = $this->sqlColumn($row['table_reference'])['rows'] ?? null;
            $columnReference = $this->sqlColumn($row['column_reference'])['rows'] ?? null;

            $selectSql['schemaJoin'][$tableReference] = ["$table.$columnNamePrimary" => "$tableReference.$columnIdReference"];
            $selectQuery[$table] = "$table.$uniqueId";
            $selectQuery[] = "$table.$columnNamePrimary";
            $selectQuery[] = "$tableReference.$columnIdReference AS id_$tableReference, $tableReference.$columnReference";

            if (isset($row['column_reference_id'])) {
                $columnReferenceId = $this->sqlColumn($row['column_reference_id']);
                $selectQuery[] = "{$columnReferenceId['key']}.{$columnReferenceId['rows']}";
            }

            $selectSql['unique_id'] = "$table.$uniqueId";
        }

        $selectSql['selects'] = implode(', ', $selectQuery);
        return $selectSql;
    }

    /**
     * Construye la configuración de formularios basada en tipos de datos.
     *
     * @return array
     */
    private function buildInputForms(): array
    {
        try {
            $cacheKey = 'input_forms_' . self::getDatabaseName();
            return Cache::remember($cacheKey, self::CACHE_TTL, function () {
                $inputs = [];
                foreach ($this->dataTypes as $table => $columns) {
                    foreach ($columns as $column => $type) {
                        $inputs[$table][$column] = match ($type) {
                            'int', 'bigint' => [
                                'id' => $column,
                                'type' => 'number',
                                'name' => $column,
                                'step' => '1',
                                'min' => '0',
                                'max' => '10000',
                                'value' => '',
                                'attributes' => [],
                            ],
                            'enum' => [
                                'id' => $column,
                                'type' => 'text',
                                'name' => $column,
                                'value' => '',
                                'attributes' => [],
                            ],
                            'varchar' => [
                                'id' => $column,
                                'type' => 'text',
                                'name' => $column,
                                'value' => '',
                                'attributes' => [],
                            ],
                            'text' => [
                                'id' => $column,
                                'type' => 'textarea',
                                'name' => $column,
                                'value' => '',
                                'rows' => 10,
                                'cols' => 30,
                                'attributes' => [],
                            ],
                            'date' => [
                                'id' => $column,
                                'type' => 'date',
                                'name' => $column,
                                'value' => '',
                                'attributes' => [],
                            ],
                            'datetime', 'timestamp' => [
                                'id' => $column,
                                'type' => 'datetime',
                                'name' => $column,
                                'value' => '',
                                'attributes' => [],
                            ],
                            'tinyint' => [
                                'id' => $column,
                                'type' => 'checkbox',
                                'name' => $column,
                                'value' => '',
                                'attributes' => [],
                            ],
                            'decimal', 'double' => [
                                'id' => $column,
                                'type' => 'number',
                                'name' => $column,
                                'step' => '0.01',
                                'min' => '0.00',
                                'max' => '999.99',
                                'value' => '0.00',
                                'attributes' => [],
                            ],
                            'float' => [
                                'id' => $column,
                                'type' => 'text',
                                'name' => $column,
                                'value' => '',
                                'attributes' => [],
                            ],
                            default => [
                                'id' => $column,
                                'type' => 'text',
                                'name' => $column,
                                'value' => '',
                                'attributes' => [],
                            ],
                        };
                    }
                }
                return $inputs;
            });
        } catch (\Exception $e) {
            \Log::error("Error en buildInputForms: {$e->getMessage()}");
            return [];
        }
    }

    /**
     * Valida si la tabla existe.
     *
     * @param string $tableName
     * @return bool
     */
    private function validateTable(string $tableName): bool
    {
        $tables = $this->getInformationTables();
        return isset($tables[$tableName]);
    }
}