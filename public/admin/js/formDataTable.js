import selectOptionRelation from "./selectOptionRelation.js";
import { controllerDataTable } from "./controllerDataTable.js";
import moment from './moment.js';


export class formDataTable extends selectOptionRelation{
    constructor() {
        super();
    
        // Obtener la ubicación actual de la página
        this.locations = window.location;
        this.pathnames = `${this.locations.pathname}/get`;

        // Obtener el token CSRF de la página
        this.token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Obtener el nombre del formulario a partir de la ruta
        this.FormName = this.pathnames.split('/')[3];
        this.nameProcess = this.pathnames.split('/')[4];
        this.idSelected = this.pathnames.split('/')[5];

        this.formContainer = document.querySelector('#formContainerAdmin');
        this.DivBtnGroup= this.formContainer.getElementsByClassName('btn-group')[0];

        this.instance = new selectOptionRelation(this.idSelected);
        this.controller= new controllerDataTable(this.token);
        
        const now = new moment();
        this.dateFormat= now.format('YYYY-MM-DD HH:mm:ss');
       

    }

    async processData(responseObject) {
        var Objectupdate={};
        const DivGroups = [];
        for(const k in responseObject){
            if (Object.hasOwnProperty.call(responseObject, k)) {
                Objectupdate= responseObject[k];
                for (const i in Objectupdate) {
                    if (Object.hasOwnProperty.call(Objectupdate, i)) {
                        if (Objectupdate[i].id) {
                            const DataSelect = [];
                            DataSelect[i] = Objectupdate[i];
                            const DivGroup = this.createDivGroup(Objectupdate[i], DataSelect);
                            DivGroups.push(DivGroup);
                        }
                    }
                }
            }    
        } 
            
        if (Object.hasOwnProperty.call(Objectupdate, 'actions')) {
            const DivBtnGroup= this.createButtons(Objectupdate.actions)[0];
            // Agrega todos los DivGroup al contenedor o formulario apropiado
            for (const DivGroup of DivGroups) {
                this.formContainer.insertBefore(DivGroup, DivBtnGroup);
            }
        }
    }

    createDivGroup(data, Dataselect) {
        const DivGroup = document.createElement('div');
        DivGroup.classList.add('mb-3', 'row', `group-${data.id}`);
        DivGroup.classList.add('text-capitalize');
        
        if (typeof data.value === "object" && data.value !== null && !(data.value instanceof Array)) {

            DivGroup.appendChild(this.createLabel(data));
            this.instance.createSelectFromObjects(Dataselect).forEach((select) => {
                //contenedor.appendChild(select);
                DivGroup.appendChild(select);
            });

        } else if (data.type === 'textarea') {
            DivGroup.appendChild(this.createLabel(data));
            DivGroup.appendChild(this.createTextarea(data));
        } else {
            DivGroup.appendChild(this.createLabel(data));
            DivGroup.appendChild(this.createInput(data));
        }

        return DivGroup;
    }

    createLabel(data) {
        const label = document.createElement('label');
        label.classList.add('form-label');
        label.textContent = data.name;
        label.setAttribute('for', data.id);
        return label;
    }

    createInput(data) {
        const input = document.createElement('input');
        input.classList.add('form-control');
        input.id = data.id;
        input.type = data.type;
        input.name = data.name;

        switch (data.name) {
            case 'position':
                sessionStorage.setItem('position', data.value);
                break;
        }
        switch (data.type) {
            case 'datetime':
                input.value = data.value !== '' ? data.value: this.dateFormat;
                break;
            default:
                input.value = data.value;
                break;
        }
        
        if (data.hasOwnProperty('attributes')) {
            for (const key in data.attributes) {
                if (data.attributes.hasOwnProperty(key)) {
                    if(data.attributes[key] || parseInt(data.attributes[key]) > 0){
                        input.setAttribute(key, data.attributes[key]);
                    }
                }
            }
        }

        return input;
    }

    createTextarea(data) {
        const textarea = document.createElement('textarea');
        textarea.classList.add('form-control');
        textarea.id = data.id;
        textarea.name = data.name;
        textarea.value = data.value;
        if (data.hasOwnProperty('attributes')) {
            for (const key in data.attributes) {
                if (data.attributes.hasOwnProperty(key)) {
                    if(data.attributes[key] || parseInt(data.attributes[key]) > 0){
                        textarea.setAttribute(key, data.attributes[key]);
                    }
                }
            }
        }
        return textarea;
    }

    createButtons(actions) {
        const DivBtnGroup= [];
        const reversedActions = Object.keys(actions).reverse();
        const char_i = String.fromCharCode(237);
        const char_a = String.fromCharCode(225);
        const translate={
            create: `Crear art${char_i}culo`,
            update: `Actualizar art${char_i}culo`,
            back: `Volver atr${char_a}s`
        }

        for (const action of reversedActions) {
            
                const bntLink = document.createElement('a');
                bntLink.textContent = translate[action];
                bntLink.setAttribute("data-href", actions[action]);
                bntLink.setAttribute("data-name", action);
                bntLink.classList.add('btn', `btn-${action}`);
                this.DivBtnGroup.appendChild(bntLink); // Agregamos cada botón al DivBtnGroup
            
        }
        DivBtnGroup.push(this.DivBtnGroup);
        return DivBtnGroup;
    }
    
    removeDuplicatesAndEmptyOptions(select) {
        const uniqueValue = new Set();
        const optionsToRemove = [];
      
        select.querySelectorAll('option').forEach(option => {
          const trimmedValue = option.value.trim();
          const hasValueAttribute = option.hasAttribute('value');
          const hasSelectedAttribute = option.hasAttribute('selected');
      
          if (!hasValueAttribute) {
            // El nodo hijo no tiene el atributo 'value'
            console.log("La opción no tiene el atributo 'value':", option);
          }
      
          if (trimmedValue === "" || !hasValueAttribute && !hasSelectedAttribute && uniqueValue.has(trimmedValue)) {
            optionsToRemove.push(option);
          } else {
            uniqueValue.add(trimmedValue);
          }
        });
      
        for (let i = optionsToRemove.length - 1; i >= 0; i--) {
          select.removeChild(optionsToRemove[i]);
        }
      }
      

    async load() {
        console.log('init---->>load...', this.pathnames);
        try {
            const response = await Ajax.get(this.pathnames, {
                type:'json',
                cache: false,
                headers: {
                    'Content-Type': 'application/json; charset=UTF-8',
                    'X-CSRF-TOKEN': this.token,
                    'Authorization': `Bearer ${this.token}`
                },
            });
            //console.table(response);
            // Procesar los datos de respuesta
            if (typeof response !== null && response !== '') {
                await this.processData(JSON.parse(response));     
                console.log('Tiempo de carga de la página: ');
                await this.controller.processData(this.formContainer);
               const selects= this.formContainer.querySelectorAll('select');
               selects.forEach(this.removeDuplicatesAndEmptyOptions)
            }

        } catch (error) {
            console.error(error);
        }
    }
}

// Esta función DataConfigForm utiliza la clase formDataTable para obtener datos específicos



/*const DataConfigForm = async () => {
    const data = new formDataTable();
    await data.load();
   

    const formContainer = document.querySelector('#formContainerAdmin');

    for (let index = 0; index < data.arrayRows.length; index++) {
        const element = data.arrayRows[index];
        formContainer.appendChild(element);
    }
    console.table('formContainer..', data, formContainer);
};*/
