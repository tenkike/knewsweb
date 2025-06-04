/*!
 * Color mode toggler for Bootstrap's docs (https://getbootstrap.com/)
 * Copyright 2011-2023 The Bootstrap Authors
 * Licensed under the Creative Commons Attribution 3.0 Unported License.
 */

(() => {
    'use strict'
  
    const storedTheme = localStorage.getItem('theme');

    const themeSidebarMenu = document.querySelector('#sidebarMenu');
    const linksInMenu= themeSidebarMenu.querySelectorAll('li a');

    const dropdownItem = document.querySelectorAll('a.dropdown-item');
     
    const getPreferredTheme = () => {
      if (storedTheme) {
        return storedTheme
      }
  
      return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'
    }
  
    const setTheme = function (theme) {
      if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      //  document.documentElement.setAttribute('data-bs-theme', 'dark');
        
        dropdownItem.forEach(dropdownLink => {
          dropdownLink.classList.add('dark');
          dropdownLink.classList.remove('light');
        });

        linksInMenu.forEach(link => {
          const isActive = link.classList.contains('active');
          
          if(isActive){
            link.classList.add('dark');
            link.classList.remove('light');
          }else{
            link.classList.add('dark');
            link.classList.remove('light');
          }
          
        });

      } 
      else {
        //document.documentElement.setAttribute('data-bs-theme', theme);
        
        dropdownItem.forEach(dropdownLink => {
            dropdownLink.classList.remove('light');
            dropdownLink.classList.remove('dark');
            dropdownLink.classList.add(theme);
        });

        linksInMenu.forEach(link => {
          const isActive = link.classList.contains('active');
          
          if(isActive){
            link.classList.remove('light');
            link.classList.remove('dark');
            link.classList.add(theme);
          }else{
            link.classList.remove('light');
            link.classList.remove('dark');
            link.classList.add(theme);
          }
        });

      }
    
      document.documentElement.setAttribute('data-bs-theme', theme);
    }
  
    
    setTheme(getPreferredTheme())
    
    const showActiveTheme = (theme, focus = false) => {
      const themeSwitcher = document.querySelector('#bd-theme')
  
      if (!themeSwitcher) {
        return
      }
  
      const themeSwitcherText = document.querySelector('#bd-theme-text')
      const activeThemeIcon = document.querySelector('.theme-icon-active use')
      const btnToActive = document.querySelector(`[data-bs-theme-value="${theme}"]`)
      const svgOfActiveBtn = btnToActive.querySelector('svg use').getAttribute('href')
  
      document.querySelectorAll('[data-bs-theme-value]').forEach(element => {
        element.classList.remove('active')
        element.setAttribute('aria-pressed', 'false')
      })
        
      btnToActive.classList.add('active')
      btnToActive.setAttribute('aria-pressed', 'true')
      activeThemeIcon.setAttribute('href', svgOfActiveBtn)
      const themeSwitcherLabel = `${themeSwitcherText.textContent} (${btnToActive.dataset.bsThemeValue})`
      themeSwitcher.setAttribute('aria-label', themeSwitcherLabel)
  
      if (focus) {
        themeSwitcher.focus()
      }
    }
  
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
      if (storedTheme !== 'light' || storedTheme !== 'dark') {
        setTheme(getPreferredTheme())
      }
    })
  
    window.addEventListener('DOMContentLoaded', () => {
      showActiveTheme(getPreferredTheme())
      document.querySelectorAll('[data-bs-theme-value]').forEach(toggle => {
        toggle.addEventListener('click', () => {
          const theme = toggle.getAttribute('data-bs-theme-value');
          localStorage.setItem('theme', theme);
          setTheme(theme);
          showActiveTheme(theme, true);
        });
      });

      ActiveLinks();

    });

     /***active**/
    function ActiveLinks() {
      const links = document.querySelectorAll('.nav-link');
  
      const activeLinkIndex = localStorage.getItem('activeLinkIndex');
      const activeLinkIndexDropdown = localStorage.getItem('activeLinkIndexDropdown');

      if (activeLinkIndex !== null) {
        localStorage.clear('activeLinkIndexDropdown');
        links[activeLinkIndex].classList.add('active');
      }
      else if(activeLinkIndexDropdown !== null){
        localStorage.clear('activeLinkIndex');
        dropdownItem[activeLinkIndexDropdown].classList.add('active');
      }

      dropdownItem.forEach((dropdownLink, index) => {
        dropdownLink.addEventListener('click', () => {
          dropdownItem.forEach(otherLink => {
              otherLink.classList.remove('active');
            });

            dropdownLink.classList.add('active');
            localStorage.setItem('activeLinkIndexDropdown', index.toString());

        });
      })

      links.forEach((link, index) => {
        link.addEventListener('click', () => {
          // Elimina la clase "active" de todos los enlaces
          links.forEach(otherLink => {
            otherLink.classList.remove('active');
          });

          // Agrega la clase "active" al enlace que se hizo clic
          link.classList.add('active');

          // Almacena el índice del enlace activo en localStorage
          localStorage.setItem('activeLinkIndex', index.toString());

        });
      });

  }

  })()
  