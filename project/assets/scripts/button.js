// Dropdown Navbar
function dropdownNavbar(){
    let dropdown = document.getElementById("userDropdown");

    if (dropdown.style.visibility === 'hidden')
    {
        dropdown.removeAttribute('hidden');
    } else {
        dropdown.setAttribute('hidden');
    }
}
let caca = "caca";
console.log(`Webpack Encore is working`);
console.log(caca);