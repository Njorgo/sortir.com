function selectionVille(){
    let ville = document.querySelector("#creer_sortie_ville");
    ville.addEventListener("change", function(){
        fetch( /*"{{ app_url_api }}"*/
            'http://sortir.com/api/lieux/' + this.value, {
                method: "GET",
                headers: { 'Accept' : 'application/json' }
            }
        )
        .then(response => response.json())
        .then(json => {
            console.log(json);
            const lieux = document.querySelector('#creer_sortie_lieuSortie');
            lieux.innerHTML="";
            json.map(lieu=>{
                lieux.innerHTML+=`<option value="${lieu.id}">${lieu.nom}</option>`
            })
        })
        .catch(error => {
            console.log(error);
        })
    });
}
    window.onload = () => {
        selectionVille();
};