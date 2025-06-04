class selectOptionRelation{

    constructor(idSelected=0) {
        this.idSelected= idSelected;        
    }
  
    createSelectFromObjects(selectObjects) {
        var selectElements =[];
        
        for (const fieldName in selectObjects) {
            if (selectObjects.hasOwnProperty(fieldName)) {
                const fieldData = selectObjects[fieldName];
    
                if (this.isSelectField(fieldData)) {
                    const select = this.createSelectElement(fieldData);
                    selectElements.push(select);
                }
            }
        }       
          
        return selectElements;
    }

    isSelectField(fieldData) {
        return (
            typeof fieldData.value === "object" &&
            fieldData.value !== null &&
            !(fieldData.value instanceof Array)
        );
    }

    createSelectElement(data) {
        const select = document.createElement('select');
        select.classList.add('form-select');
        select.setAttribute('onchange', 'getDataSelects()')
        //select.setAttribute('size', 2);
        select.id = data.id;
        select.name = data.name;
        if (data.hasOwnProperty('attributes')) {
            for (const key in data.attributes) {
                if (data.attributes.hasOwnProperty(key)) {
                    if(data.attributes[key] === true){
                        select.setAttribute(key, data.attributes[key]);
                    }
                }
            }
        }

        for (const optionValue in data.value) {
            if (data.value.hasOwnProperty(optionValue)) {
                const option = this.createOptionElement(data.name, optionValue, data.value[optionValue]);
                select.appendChild(option);
            }
        }
            
        return select;
    }

    processStorage(n, find){
        // Obtener todas las claves de sessionStorage
        const keys = Object.keys(n);

        // Filtrar las claves que comienzan con 'find'
        const dependentKeys = keys.filter(key => key.startsWith(find));

        // Obtener los valores correspondientes a esas claves
        return dependentKeys.map(key => sessionStorage.getItem(key));

    }

    createOptionElement(colName, optionValue, OptionData) {
        const option = document.createElement('option');
        
        const keys = Object.keys(OptionData); 
        if(sessionStorage.getItem('idCheck') !== this.idSelected){
                sessionStorage.clear();
        }
        switch (colName) {
            case 'status':
                const data = OptionData[keys[0]];
               
                let values = []; // Inicializa el valor en 0
                //let names = ''; // Inicializa el nombre como una cadena vacía

                for (const items in data) {
                    const item = data[items];
                    const enums = item.enum;
                    const IntKey = items;

                    if (enums.hasOwnProperty('selected')) {
                        option.value= parseInt(IntKey); 
                        option.text= enums.name;
                        option.classList.add('bg-success');
                        option.setAttribute('selected', enums.selected);
                    } 
                    else {
                        option.value= parseInt(IntKey); 
                        option.name= enums.name;
                    }
                }

                break;
            case 'id_parent':
                    //console.log('id_parent', optionValue, OptionData);                
                break;
            
            case 'id_name':
                   
                if (parseInt(this.idSelected) === parseInt(optionValue)) {
                    
                    option.text= OptionData[keys[0]];
                    option.value= keys[0];
                    option.setAttribute('selected', true)
                    option.classList.add('bg-success');
                    sessionStorage.setItem('idCheck', this.idSelected);
                    sessionStorage.setItem('selected', keys[0]);
                    //console.log('id_name select', optionValue, keys[0], OptionData[keys[0]]);                
                
                }else if(typeof OptionData !== 'object'){
                    option.text= OptionData;
                    option.value= optionValue;
                }
                   
                break;
                
            case 'id_sublink':
                const selected= sessionStorage.getItem('selected');
                if (parseInt(selected) === parseInt(optionValue)) {
                    option.value= 'dependent';
                    console.log('OptionData>>>>', OptionData);
                   for(const i in OptionData){
                    if(i === keys[0]){
                        option.appendChild(new Option(OptionData[i], i)).classList.add('bg-success');
                    }else{
                        option.appendChild(new Option(OptionData[i], i));
                    }
                   }
                }
                break;
        
            default:
                
                //console.log('default 2', optionValue, keys[0], OptionData);
                                
                break;
        }

        
        return option;
    }
}

export default selectOptionRelation