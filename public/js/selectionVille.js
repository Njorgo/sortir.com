/*window.onload = () => {
    let ville = document.querySelector("#creer_sortie_ville");*/
    var globalLieux;
    function getSelectValue(selectId)
    {
        var selectElmt = document.getElementById(selectId);
        return selectElmt.options[selectElmt.selectedIndex].value;
    }
    function initLieux() {
    /*ville.addEventListener("change", function(){*/
        fetch( /*"{{ app_url_api }}"*/'http://localhost:8000/api/lieux' + getSelectValue('creer_sortie_ville'), {
            method: "GET",
            headers: { 'Accept' : 'application/json' }
        })
        /*let form = this.closest("form");
        let data = this.name + "=" + this.value ;*/

       /* fetch(form.action, {
            method: form.getAttribute("method"),
            body: data,
            headers: {
                "content-type": "application/x-www-form-urlencoded; charset:utf-8"
            }
        })*/
            .then(response => response.json())
            .then(response => {

                globalLieux = response;
                        let options="";
                        response.map(lieu => {
                            options += `<option value="${lieu.id}">${lieu.nom}</option>`;
                        })
                        // injecter le résultat dans <select>
                        document.querySelector("#creer_sortie_lieuSortie").innerHTML = options;
                        // afficher détail lieu
                        detailLieu();
                    }
                )
                .catch(e => {
                    alert("ERREUR")
                })
        }
        /*function detailLieu() {
            function findCurrentLieu(lieu) {
                if (lieu.id == getSelectValue('creer_sortie_lieuSortie')){
                    document.querySelector("#lieu_rue").innerHTML = lieu.rue;
                    document.querySelector("#lieu_code_postal").innerHTML = lieu.ville.codePostal;
                    document.querySelector("#lieu_latitude").innerHTML = lieu.latitude;
                    document.querySelector("#lieu_longitude").innerHTML = lieu.longitude;
                }
            }
            globalLieux.forEach(findCurrentLieu)
        }*/
        window.onload = () => {
            document.querySelector("#creer_sortie_ville").addEventListener("change", function () {
                initLieux();
            })
            /*document.querySelector("#creer_sortie_lieuSortie").addEventListener("change", function () {
                detailLieu();
            })*/
            initLieux();
        }

                /*const lieux = document.querySelector('#creer_sortie_lieuSortie');
                    lieux.innerHTML="";
                    json.map(lieu=>{
                        lieux.innerHTML+=`<option value="${lieu.id}">${lieu.nom}</option>`
                    })
                })*/


                /*let content = document.createElement("html");
                content.innerHTML = html;
                let nouveauSelect = content.querySelector("#creer_sortie_lieuSortie");
                document.querySelector("#creer_sortie_lieuSortie").replaceWith(nouveauSelect);
            })*/
            /*.catch(e => {
                alert("ERREUR")
            })*/
    /*})
}*/