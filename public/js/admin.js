let salesBtn           = document.querySelector("#sales-nav");
let productionBtn      = document.querySelector("#production-nav");
let salesSubNavs       = document.querySelectorAll("#sales-sub-nav");
let productionSubNavs  = document.querySelectorAll("#production-sub-nav");

window.scroll = () => {
    salesSubNavs.forEach( sales => {
        sales.classList.remove('active');
    });

    productionSubNavs.forEach( production => {
        production.classList.remove('active');
    });
}

salesBtn.addEventListener('click', () => {
    salesSubNavs.forEach( sales => {
        sales.classList.toggle('active');
    });
});

productionBtn.addEventListener('click', () => {
    productionSubNavs.forEach( production => {
        production.classList.toggle('active');
    });
});