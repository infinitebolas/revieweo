<script>
    const btn_usr = document.querySelector(".btn_usr");

    if (btn_usr) {
        btn_usr.addEventListener("click", () => {

            const container = document.getElementById("user");


            if (container.innerHTML !== "") {
                container.innerHTML = "";
                return;
            }

            fetch("liste_user.php")
                .then(response => response.json())
                .then(data => {
                    data.forEach(user => {

                        const card = document.createElement("div");
                        card.className = "card mb-2";
                        card.style.width = "18rem";

                        const cardBody = document.createElement("div");
                        cardBody.className = "card-body";


                        const pseudo = document.createElement("h5");
                        pseudo.className = "card-title";
                        pseudo.textContent = user.pseudo;
                        cardBody.appendChild(pseudo);


                        const role = document.createElement("p");
                        role.className = "card-text";
                        role.textContent = "Rôle : " + user.role;
                        cardBody.appendChild(role);


                        const btnDelete = document.createElement("button");
                        btnDelete.className = "btn btn-danger";
                        btnDelete.textContent = "Supprimer";
                        btnDelete.addEventListener("click", () => {
                            fetch("delete_user.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/x-www-form-urlencoded"
                                    },
                                    body: `pseudo=${encodeURIComponent(user.pseudo)}`
                                })
                                .then(res => res.json())
                                .then(result => {
                                    if (result.success) {
                                        card.remove();
                                    } else {
                                        alert("Erreur : " + result.message);
                                    }
                                })
                                .catch(err => console.error("Erreur :", err));
                        });
                        cardBody.appendChild(btnDelete);

                        card.appendChild(cardBody);
                        container.appendChild(card);
                    });
                })
                .catch(error => console.error("Erreur :", error));
        });
    }


    const btn_crit = document.querySelector(".btn_crit");

    if (btn_crit) {
        btn_crit.addEventListener("click", () => {

            const container = document.getElementById("critique");

            if (container.innerHTML !== "") {
                container.innerHTML = "";
                return;
            }

            fetch("liste_critique.php")
                .then(response => response.json())
                .then(data => {
                    data.forEach(critique => {

                        const card = document.createElement("div");
                        card.className = "card mb-2";

                        const cardBody = document.createElement("div");
                        cardBody.className = "card-body";

                        const titre = document.createElement("h5");
                        titre.className = "card-title";
                        titre.textContent = critique.titre;
                        cardBody.appendChild(titre);

                        const contenu = document.createElement("p");
                        contenu.className = "card-text";
                        contenu.textContent = critique.contenu;
                        cardBody.appendChild(contenu);

                        const auteur = document.createElement("p");
                        auteur.className = "card-text";
                        auteur.textContent = "Auteur : " + critique.pseudo;
                        cardBody.appendChild(auteur);


                        const btnGroup = document.createElement("div");
                        btnGroup.className = "mt-2 d-flex gap-2";


                        const btnDelete = document.createElement("button");
                        btnDelete.className = "btn btn-danger";
                        btnDelete.textContent = "Supprimer";

                        btnDelete.addEventListener("click", () => {
                            fetch("delete_critique.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/x-www-form-urlencoded"
                                    },
                                    body: `id_critique=${encodeURIComponent(critique.id)}`
                                })
                                .then(res => res.json())
                                .then(result => {
                                    if (result.success) {
                                        card.remove();
                                    } else {
                                        alert("Erreur : " + result.error);
                                    }
                                })
                                .catch(err => console.error("Erreur :", err));

                        });


                        const btnEdit = document.createElement("button");
                        btnEdit.className = "btn btn-warning";
                        btnEdit.textContent = "Modifier";

                        btnEdit.addEventListener("click", () => {
                            window.location.href = "admin.html";
                        });


                        const btnPin = document.createElement("button");
                        btnPin.className = "btn btn-info";
                        btnPin.textContent = "Épingler";


                        fetch("check_epingle.php", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/x-www-form-urlencoded"
                                },
                                body: `id_critique=${encodeURIComponent(critique.id)}`
                            })
                            .then(res => res.json())
                            .then(result => {
                                if (result.success && result.epingle) {
                                    btnPin.textContent = "Retirer l’épingle";
                                    card.classList.add("border-info");
                                }
                            });


                        btnPin.addEventListener("click", () => {
                            const action = btnPin.textContent === "Épingler" ? "epingle.php" : "remove_epingle.php";

                            fetch(action, {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/x-www-form-urlencoded"
                                    },
                                    body: `id_critique=${encodeURIComponent(critique.id)}`
                                })
                                .then(res => res.json())
                                .then(result => {
                                    if (result.success) {
                                        if (action === "epingle.php") {
                                            btnPin.textContent = "Retirer l’épingle";
                                            card.classList.add("border-info");
                                            container.prepend(card);
                                        } else {
                                            btnPin.textContent = "Épingler";
                                            card.classList.remove("border-info");
                                            container.appendChild(card);
                                        }
                                    } else {
                                        alert("Erreur lors de l’action sur l’épingle.");
                                    }
                                })
                                .catch(err => console.error("Erreur :", err));
                        });


                        btnGroup.appendChild(btnEdit);
                        btnGroup.appendChild(btnDelete);
                        btnGroup.appendChild(btnPin);

                        cardBody.appendChild(btnGroup);
                        card.appendChild(cardBody);
                        container.appendChild(card);
                    });
                })
                .catch(error => console.error("Erreur :", error));
        });
    }
</script>