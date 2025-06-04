class Ajax {
    static get(url, options = {}) {
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('GET', url);
  
        for (const [header, value] of Object.entries(options.headers || {})) {
          xhr.setRequestHeader(header, value);
        }
  
        xhr.onload = () => {
          if (xhr.status >= 200 && xhr.status < 300) {
            resolve(xhr.response);
          } else {
            reject(xhr.statusText);
          }
        };
  
        xhr.onerror = () => reject(xhr.statusText);
  
        xhr.send();
      });
    }
  
    static post(url, data, options = {}) {
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url);
  
        for (const [header, value] of Object.entries(options.headers || {})) {
          xhr.setRequestHeader(header, value);
        }
  
        xhr.onload = () => {
          if (xhr.status >= 200 && xhr.status < 300) {
            resolve(xhr.response);
          } else {
            reject(xhr.statusText);
          }
        };
  
        xhr.onerror = () => reject(xhr.statusText);
  
        xhr.send(data);
      });
    }

    /*****DELETE**/
    static delete(url, data, options = {}) {
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('DELETE', url);
  
        for (const [header, value] of Object.entries(options.headers || {})) {
          xhr.setRequestHeader(header, value);
        }
  
        xhr.onload = () => {
          if (xhr.status >= 200 && xhr.status < 300) {
            resolve(xhr.response);
          } else {
            reject(xhr.statusText);
          }
        };
  
        xhr.onerror = () => reject(xhr.statusText);
  
        xhr.send(data);
      });
    }
    /*****END DELETE*** */
  }
  

  /*****************/
class AjaxInterval {
    constructor(url, interval) {
      this.url = url;
      this.interval = interval;
      this.timer = null;
    }
  
    start() {
      if (this.timer !== null) {
        console.log('Timer already started');
        return;
      }
  
      console.log('Starting timer');
      this.timer = setInterval(() => {
        this._makeRequest()
          .then(text => console.log(text))
          .catch(error => console.error(error));
      }, this.interval);
    }
  
    stop() {
      if (this.timer === null) {
        console.log('Timer not started yet');
        return;
      }
  
      console.log('Stopping timer');
      clearInterval(this.timer);
      this.timer = null;
    }
  
    _makeRequest() {
      console.log('Making request to', this.url);
      return fetch(this.url)
        .then(response => {
          if (response.ok) {
            return response.json();
            //return response.text();
          } else {
            throw new Error('Network response was not ok');
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }
  }
  
 


/**
 * 
 * 
 * import Ajax from './ajax.js';

// Usa la clase Ajax para hacer una solicitud GET
Ajax.get('https://jsonplaceholder.typicode.com/posts/1')
  .then(response => console.log(response))
  .catch(error => console.error(error));

// Usa la clase Ajax para hacer una solicitud POST
const data = { title: 'foo', body: 'bar', userId: 1 };
Ajax.post('https://jsonplaceholder.typicode.com/posts', JSON.stringify(data), {
  headers: {
    'Content-type': 'application/json; charset=UTF-8'
  }
})
  .then(response => console.log(response))
  .catch(error => console.error(error));
**/