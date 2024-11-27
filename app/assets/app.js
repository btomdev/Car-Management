import './bootstrap.js';

//
import serialize from 'form-serialize';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// HTML Background random color
let html = document.documentElement;
html.style.setProperty('--rand', Math.random());





export default function ajaxFormUpdate() {

    const form = document.getElementById('car_form');
    const form_car_type = document.getElementById('car_type');

    const updateForm = async (data, url, method) => {

        const request = new XMLHttpRequest();
        request.open('POST', form.action, true);
        request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        request.onreadystatechange = function () {
            if (this.readyState === XMLHttpRequest.DONE) {
                if (this.status === 200) {
                    const content = document.createElement('html');
                    content.innerHTML = request.responseText;
                    console.log(content);
                    const formContainer = content.querySelector('#car_form');
                    console.log(formContainer);
                    form.innerHTML = formContainer.innerHTML;
                } else {
                }
            }
        };
        request.send(serialize(form));
    };

    const parseTextToHtml = (text) => {
        const parser = new DOMParser();
        const html = parser.parseFromString(text, 'text/html');

        return html;
    };

    const changeOptions = async (form) => {
        await updateForm(form, form.action, form.method);
        ajaxFormUpdate();
    };

    form_car_type.addEventListener(
        'change',
        () => changeOptions(form),
        form
    );
}


ajaxFormUpdate();