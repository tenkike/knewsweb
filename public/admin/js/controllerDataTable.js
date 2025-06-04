
export class controllerDataTable {

    constructor(token){

        this.token= token;
        /*this.pathnames= objects.pathnames;
        this.data= objects.data;*/
        //this.form= objects.form;
    }

    async processData(form){
        const links= form.querySelectorAll('a.btn');
        const selects= form.querySelectorAll('select');
        const inputs= form.querySelectorAll('input');
        const textarea= form.querySelectorAll('textarea');
        //this.processSelects(selects);

        links.forEach((link) => {
            link.addEventListener('click', async (event) =>{
                event.preventDefault(); 
                const dataHref= event.target.dataset.href;
                const dataName= event.target.dataset.name;
                const formDataArray= await this.processFormData(form);
                
                const urlParts= dataHref.split('/'); // Divide la URL en segmentos
                const url = urlParts.slice(3).join('/');
                
                const checkInputs= await this.validatorForms(inputs, formDataArray);
                const checkSelects= await this.validatorForms(selects, formDataArray);
                const checkTextarea= await this.validatorForms(textarea, formDataArray);
                
                
                console.table('checkTextarea', checkInputs, checkSelects, checkTextarea);

                if (checkInputs === false || checkSelects === false || checkTextarea === false) {
                    return false;
                }
                /***
                 * 
                 * 
                 */
                const sendData= {
                    url:`/${url}`,
                    data: formDataArray
                };
                
                console.table('objects', sendData);

                switch (dataName) {
                    case 'update':
                        await this.sendData(sendData);
                        break;
                    case 'create':
                        await this.sendData(sendData);
                        break;
                    case 'back':
                        console.table('objects', dataHref, dataName);
                        break;
                }
            });
        });     
    }

    async processFormData(form) {
        const formData = new FormData(form); 
        const formDataArray = Array.from(formData.entries());
        const dataObject={};
        for (const item of formDataArray) {
            const [name, value] = item;
            dataObject[name]= value;
        }
        return dataObject;
    }

    async inputAttributes(inputs){
        let formInput={};
        let formType={};
        
        inputs.forEach((input)=> {
            
            let inputName= input.getAttribute('name');
            
            if(input.hasAttribute('type')){
                let inputType= input.getAttribute('type');

                switch (inputType) {
                    case 'text':
                        formType[inputName]= 4;
                        break;
                }
                    
                formInput[inputName]={
                    required: input.getAttribute('required') !== null? true:false,
                    maxlength: input.getAttribute('maxlength') !== null ? input.getAttribute('maxlength'):0,
                    max: input.getAttribute('max') !== null ? input.getAttribute('max'):0,
                    min: input.getAttribute('min') !== null ? input.getAttribute('min'):0
                };
            }else{
                formType[inputName]= input.hasAttribute('required') === true ? 80: null;
                formInput[inputName]={
                    required: input.getAttribute('required') !== null? true:false,
                    maxlength: input.getAttribute('maxlength') !== null ? input.getAttribute('maxlength'):0,
                };
            }
        });

        return {formInput, formType};
    }

    async validatorForms(inputs, data) {
        const Attributes = await this.inputAttributes(inputs);
        //console.table(inputs);
        const formInputAttributes= Attributes.formInput;
        const formType= Attributes.formType;
        const inputValues = data;
        for (const fieldName in inputValues) {
            if (formInputAttributes.hasOwnProperty(fieldName)) {
                const value = inputValues[fieldName];
                const attributes = formInputAttributes[fieldName];
                const minlengthText= formType[fieldName];

                const label = document.querySelector(`label[for="${fieldName}"]`);
                const input = document.querySelector(`#${fieldName}`);
        
                if (attributes.required && (value === null || value === '')) {
                    alert(`${fieldName} es obligatorio.`);
                    label.classList.add('text-danger');
                    input.classList.add('border', 'border-danger-subtle')
                    return false;
                }
                
                if (attributes.maxlength > 0 && value.length > attributes.maxlength) {
                    alert(`${fieldName} excede la longitud máxima permitida.`);
                    label.classList.add('text-danger');
                    input.classList.add('border-danger-subtle')
                    return false;
                }

                if (attributes.maxlength > 0 && value.length < minlengthText) {
                    alert(`${fieldName} la longitud mínima permitida es de ${minlengthText} caracteres.`);
                    label.classList.add('text-danger');
                    input.classList.add('border-danger-subtle')
                    return false;
                }
                
                if (attributes.max > 0 && value > attributes.max) {
                    alert(`${fieldName} supera el valor máximo permitido.`);
                    label.classList.add('text-danger');
                    input.classList.add('border-danger-subtle')
                    return false;
                }
                
                if (attributes.min > 0 && value < attributes.min) {
                    alert(`${fieldName} es menor que el valor mínimo permitido.`);
                    label.classList.add('text-danger');
                    input.classList.add('border-danger-subtle')
                    return false;
                }

                if (label.classList.contains('text-danger')){
                    label.classList.remove('text-danger');
                }
                else{
                    label.classList.add('text-success')
                }
                
                if (input.classList.contains('border-danger-subtle')) {
                    input.classList.remove('border-danger-subtle')
                }else{
                    input.classList.add('animated-bg', 'border-success-subtle')
                }
            }
        }

    }

    async processSelects(){
        console.log('processSelects')

        
     /*   selects.forEach(select => {
            var valoresUnicos = new Set();
            
            for (var i = select.options.length - 1; i >= 0; i--) {
                var option = select.options[i];
                
                
                if (option.value.trim() === "" || option.childNodes.length === 0) {
                // Si el valor está vacío o es un duplicado, elimina la opción
                select.remove(i);
                } else {
                // Agrega el valor al conjunto de valores únicos
                valoresUnicos.add(option.value);
                }
            }

            
        });*/
        
    }

    async sendData(object) {
        console.log('init---->>send data...', this, object.url);
        try {
            const response = await Ajax.post(object.url, JSON.stringify(object.data), {
                headers: {
                    'Content-Type': 'application/json; charset=UTF-8',
                    'X-CSRF-TOKEN': this.token,
                    'Authorization': `Bearer ${this.token}`
                },
            });
            //console.table(response);
            // Procesar los datos de respuesta
            if (typeof response !== null && response !== '') {
                console.table(response);
            }

        } catch (error) {
            console.error(error);
        }
    }

}