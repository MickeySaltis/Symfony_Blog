// Dropdown Navbar
// function dropdownNavbar(){
//     let dropdown = document.getElementById("userDropdown");

//     if (dropdown.style.visibility === 'hidden')
//     {
//         dropdown.removeAttribute('hidden');
//     } else {
//         dropdown.setAttribute('hidden');
//     }
// }

export default class ButtonNavbar {
    
    constructor(profilNavbar) {
        this.profilNavbar = profilNavbar;

        if (this.profilNavbar) {
            this.init();
        }
    }

    init() {
        this.profilNavbar[0].addEventListener('click', this.onClick)
    }

    onClick() {
        const profil = document.getElementById('userDropdown');
        profil.classList.toggle('hidden');
    }
}
