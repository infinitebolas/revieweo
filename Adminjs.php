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
                    data.forEach(pseudo => {
                        const div = document.createElement("div");
                        const p = document.createElement("p");
                        p.textContent = pseudo;
                        div.appendChild(p);


                        const btnDelete = document.createElement("button");
                        btnDelete.textContent = "Supprimer";
                        btnDelete.addEventListener("click", () => {

                            fetch("delete_user.php", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/x-www-form-urlencoded"
                                    },
                                    body: `pseudo=${encodeURIComponent(pseudo)}`
                                })
                                .then(res => res.json())
                                .then(result => {
                                    if (result.success) {
                                        div.remove();
                                    } else {
                                        alert("Erreur : " + result.message);
                                    }
                                })
                                .catch(err => console.error("Erreur :", err));
                        });

                        div.appendChild(btnDelete);
                        container.appendChild(div);
                    });
                })
                .catch(error => console.error("Erreur :", error));
        });
    }
</script>