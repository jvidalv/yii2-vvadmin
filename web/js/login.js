// self executing function here
(function () {
    onEnterOrSeguent();
})();

//capturem enter
function onEnterOrSeguent() {
    $("#seguent-usuari").on("click", function (e) {
        userExist();
        e.preventDefault();
    });
    $("#loginform-email").on("keyup", function (e) {
        if (e.keyCode == 13) {
            userExist();
            e.preventDefault();
        }
    });
    $("#seguent-password").on("click", function (e) {
        comprobarContrassenya();
        e.preventDefault();
    });
    $("#loginform-password").on("keyup", function (e) {
        if (e.keyCode == 13) {
            comprobarContrassenya();
            e.preventDefault();
        }
    });
}

//quan lajax retorna un usuari
function onAjaxSuccess(response) {
    var cP = $("#container-password");
    cP.addClass("apareixer-de-baix");
    cP.find("#empresa_imatge-response").attr("src", response.imatge);
    cP.find("#nom-response").html(response.nom);
    setTimeout(function () {
        cP.find("#loginform-password").focus();
    }, 800);
}

// Crida ajax comprobar si existex usuari
function userExist() {
    var val = $("#loginform-email").val();
    if (val) {
        $.ajax({
            url: "/site/existeix-usuari",
            type: "post",
            data: {data: val}
        }).done(response => {
            if (response.status) {
                $("#loginform-email").blur();
                $("#loginform-email").removeClass("br-red");

                onAjaxSuccess(response);

                $("#container-email")
                    .addClass("desapareixer-esquerra")
                    .hide("fast");
                $("#container-password").addClass("d-block");
            } else {
                mostrarLabelError("email", response.error);
                $("#loginform-email").addClass("br-red");
                setTimeout(function () {
                    $("#loginform-email").removeClass("br-red");
                }, 5000);
            }
        });
    }
}

// Crida ajax comprobar contrassenya
function comprobarContrassenya() {
    var data = $("#login-form").serializeArray();
    if (data) {
        $.ajax({
            url: "/site/contrassenya-correcta",
            type: "post",
            dataType: "json",
            data: data
        }).done(response => {
            if (!response.status) {
                mostrarLabelError("password", response.error);
                $("#loginform-password").addClass("br-red");
                setTimeout(function () {
                    $("#loginform-password").removeClass("br-red");
                }, 3000);
            }
        });
    }
}

//on mostrar lerror
function mostrarLabelError(tipo, missatge) {
    if (tipo == "email") {
        $("#error-label-email")
            .html(missatge)
            .show("fast");
        setTimeout(function () {
            $("#error-label-email")
                .html(missatge)
                .hide("fast");
        }, 5000);
    }
    if (tipo == "password") {
        $("#error-label-password")
            .html(missatge)
            .show("fast");
        setTimeout(function () {
            $("#error-label-password")
                .html(missatge)
                .hide("fast");
        }, 3000);
    }
}
