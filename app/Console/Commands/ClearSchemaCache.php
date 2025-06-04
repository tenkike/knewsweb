<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Library\Crud\DataSchema;

class ClearSchemaCache extends Command
{
    protected $signature = 'schema:clear-cache';
    protected $description = 'Limpia el caché de los datos del esquema de la base de datos';

    public function handle()
    {
        $dbName = DataSchema::getDatabaseName();

        Cache::forget('schema_tables_' . $dbName);
        Cache::forget('schema_tables_data_' . $dbName);
        Cache::forget('input_forms_' . $dbName);

        $tables = DataSchema::getInformationTables();
        if ($tables) {
            foreach (array_keys($tables) as $table) {
                Cache::forget('schema_columns_' . $dbName . '_' . $table);
                Cache::forget('schema_relations_' . $dbName . '_' . $table);
                Cache::forget('schema_info_' . $dbName . '_' . $table);
                Cache::forget('schema_relations_info_' . $dbName . '_' . $table);
                Cache::forget('form_config_form_' . $dbName . '_' . $table);
                Cache::forget('data_db_' . $dbName . '_' . $table);
                Cache::forget('join_relation_' . $dbName . '_' . $table);
            }
        }

        $this->info('Caché del esquema limpiado correctamente.');
    }
}