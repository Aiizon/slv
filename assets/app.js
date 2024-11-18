import './styles/bootstrap.scss';
import './styles/app.scss';
import * as bootstrap from 'bootstrap'

// handle display of the user popup

const userPopup = document.querySelector('#userPopup');
const userPopupBtn = document.querySelector('#userPopupBtn');

userPopupBtn.addEventListener('click', () => {
    userPopup.classList.toggle('d-none');
});