'use strict';

class GridDataTable {
    constructor() {
        this.location = window.location;
        this.pathname = `${this.location.pathname}/get`;
        this.token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.tableName = this.pathname.split('/')[3] || 'default';
        this.tableContainer = document.getElementById(`content-${this.tableName}`);
        console.log('TableName:', this.tableName);
        console.log('CSRF Token:', this.token);
        console.log('AJAX URL:', this.pathname);

        if (!this.token) {
            console.error('No se encontró el token CSRF');
            if (this.tableContainer) {
                this.tableContainer.innerHTML = '<p>Error: Configuración inválida (falta token CSRF).</p>';
            }
            return;
        }
        if (!this.tableContainer) {
            console.error(`No se encontró el contenedor content-${this.tableName}`);
            return;
        }
    }

    initialize() {
        const tableElement = document.getElementById(this.tableName);
        if (!tableElement) {
            console.error(`No se encontró la tabla con ID #${this.tableName}`);
            if (this.tableContainer) {
                this.tableContainer.innerHTML = '<p>Error: No se encontró la tabla.</p>';
            }
            return null;
        }

        try {
            const table = new DataTable(`#${this.tableName}`, {
                ajax: {
                    url: this.pathname,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': this.token,
                    },
                    dataSrc: function (json) {
                        console.log('Respuesta JSON:', json);
                        if (!json || !json.data) {
                            console.warn('Respuesta JSON inválida o vacía:', json);
                            return [];
                        }
                        if (json.data.length === 0 && json.recordsTotal > 0) {
                            console.warn(`No se recibieron datos, pero hay ${json.recordsTotal} registros en total`);
                            tableElement.parentElement.insertAdjacentHTML('beforebegin', `<p class="text-warning">No se pudieron cargar los datos (hay ${json.recordsTotal} registros). Contacte al administrador.</p>`);
                        }
                        // Almacenar en caché
                        const cacheKey = `grid_${this.tableName}`;
                        localStorage.setItem(cacheKey, JSON.stringify({ data: json, timestamp: Date.now() }));
                        return json.data;
                    }.bind(this), // Asegura que 'this' sea GridDataTable
                    error: (xhr, error, thrown) => {
                        console.error('Error en la solicitud AJAX:', {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            response: xhr.responseJSON || xhr.responseText,
                            error: error,
                            thrown: thrown,
                        });
                        if (this.tableContainer) {
                            this.tableContainer.innerHTML = `<p>Error al cargar la grilla (Código: ${xhr.status}). ${xhr.responseJSON?.message || 'Inténtalo de nuevo.'}</p>`;
                        }
                    },
                },
                serverSide: true,
                processing: true,
                responsive: true,
                pagingType: 'full_numbers',
                language: {
                    emptyTable: 'No hay datos para mostrar',
                    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                    lengthMenu: 'Mostrar _MENU_ registros',
                    search: 'Buscar:',
                    paginate: {
                        first: 'Primero',
                        last: 'Último',
                        next: 'Siguiente',
                        previous: 'Anterior',
                    },
                },
                drawCallback: () => {
                    console.log('Draw callback ejecutado');
                    this.customizeButtons();
                },
            });

            table.on('xhr.dt', (e, settings, json) => {
                console.log('Evento xhr.dt ejecutado', json);
                if (!json || !json.data || !json.data.length) {
                    console.warn('No hay datos para generar columnas:', json);
                    table.clear().draw();
                    return;
                }

                try {
                    const columns = Object.keys(json.data[0]).map(key => ({
                        data: key,
                        title: key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()),
                        render: (data, type, row) => {
                            if (key === 'actions' && data) {
                                try {
                                    return Object.keys(data).map(action => {
                                        const icon = action === 'delete' ? 'trash' :
                                                    action === 'update' ? 'pen-to-square' : 'images';
                                        return `<a href="${data[action]}" class="btn btn-sm btn-${action === 'delete' ? 'danger' : 'primary'}" title="Click to ${action}">
                                            <i class="fa-solid fa-${icon} fa-sm"></i>
                                        </a>`;
                                    }).join(' ');
                                } catch (err) {
                                    console.error('Error al renderizar acciones:', err, data);
                                    return '';
                                }
                            }
                            const div = document.createElement('div');
                            div.textContent = data != null ? data : '';
                            return div.innerHTML;
                        },
                    }));

                    console.log('Columnas generadas:', columns);
                    table.columns(columns).draw();
                } catch (err) {
                    console.error('Error al generar columnas:', err);
                    table.clear().draw();
                }
            });

            // Cargar desde caché si está disponible
            const cacheKey = `grid_${this.tableName}`;
            const cached = localStorage.getItem(cacheKey);
            const cacheTTL = 5 * 60 * 1000; // 5 minutos
            if (cached) {
                const { data, timestamp } = JSON.parse(cached);
                if (Date.now() - timestamp < cacheTTL) {
                    console.log('Cargando datos desde caché');
                    if (data && data.data && data.data.length) {
                        const columns = Object.keys(data.data[0]).map(key => ({
                            data: key,
                            title: key.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()),
                            render: (data, type, row) => {
                                if (key === 'actions' && data) {
                                    return Object.keys(data).map(action => {
                                        const icon = action === 'delete' ? 'trash' :
                                                    action === 'update' ? 'pen-to-square' : 'images';
                                        return `<a href="${data[action]}" class="btn btn-sm btn-${action === 'delete' ? 'danger' : 'primary'}" title="Click to ${action}">
                                            <i class="fa-solid fa-${icon} fa-sm"></i>
                                        </a>`;
                                    }).join(' ');
                                }
                                const div = document.createElement('div');
                                div.textContent = data != null ? data : '';
                                return div.innerHTML;
                            },
                        }));
                        table.columns(columns).data().clear().rows.add(data.data).draw();
                    }
                }
            }

            return table;
        } catch (err) {
            console.error('Error al inicializar DataTables:', err);
            if (this.tableContainer) {
                this.tableContainer.innerHTML = '<p>Error al inicializar la tabla. Contacte al administrador.</p>';
            }
            return null;
        }
    }

    customizeButtons() {
        try {
            document.querySelectorAll('.btn-group a').forEach(link => {
                const action = link.classList.contains('btn-delete') ? 'delete' :
                              link.classList.contains('btn-update') ? 'update' : 'images';
                link.innerHTML = `<i class="fa-solid fa-${action === 'delete' ? 'trash' : action === 'update' ? 'pen-to-square' : 'images'} fa-sm"></i>`;
                link.title = `Click to ${action}`;
            });
        } catch (err) {
            console.error('Error en customizeButtons:', err);
        }
    }
}