/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import 'tw-elements';
import Like from './scripts/like';
import ButtonNavbar from './scripts/buttonNavbar';


document.addEventListener('DOMContentLoaded', () =>{
    
    /* Like System */
    const likeElements = [].slice.call(document.querySelectorAll('a[data-action="like"]'));
    if(likeElements)
    {
        new Like(likeElements);
    }

    /* Profil Navbar System */
    const profilNavbar = document.querySelectorAll('img[data-action="profilNavbar"]');
    if(profilNavbar)
    {
        new ButtonNavbar(profilNavbar);
    }
})

// start the Stimulus application
// import './bootstrap';

// console.log(`Webpack Encore is working`);
