/**
 * THIS CODE IS OLD AND NEEDS TO BE REFACTORED AND SIMPLIEFIED
 */
(function () {
    onEnterOrSeguent();
})();

/**
 * Capture enter button so you can't enter and only click button
 */
function onEnterOrSeguent() {
    $("#seguent-usuari").on("click", function (e) {
        userExist();
        e.preventDefault();
    });
    $("#loginform-email").on("keyup", function (e) {
        if (e.keyCode === 13) {
            userExist();
            e.preventDefault();
        }
    });
    $("#seguent-password").on("click", function (e) {
        comprobarContrassenya();
        e.preventDefault();
    });
    $("#loginform-password").on("keyup", function (e) {
        if (e.keyCode === 13) {
            comprobarContrassenya();
            e.preventDefault();
        }
    });
}

/**
 * Throws when ajax returns successful
 * @param response
 */
function onAjaxSuccess(response) {
    let cP = $("#container-password");
    cP.addClass("apareixer-de-baix");
    cP.find("#empresa_imatge-response").attr("src", response.imatge);
    cP.find("#nom-response").html(response.nom);
    setTimeout(function () {
        cP.find("#loginform-password").focus();
    }, 800);
}

/**
 * Checks if user exists using and ajax call
 */
function userExist() {
    const val = $("#loginform-email").val();
    if (val) {
        $("#seguent-usuari").append('<i class="fas fa-circle-notch fa-spin ml-2"></i>');
        $.ajax({
            url: "/site/existeix-usuari",
            type: "post",
            data: {data: val}
        }).always(() => {
            $('.fa-circle-notch').remove();
        }).done(response => {
            $('.fa-circle-notch').remove();
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

/**
 * Checks for user password
 */
function comprobarContrassenya() {
    const data = $("#login-form").serializeArray();
    if (data) {
        $("#seguent-password").append('<i class="fas fa-circle-notch fa-spin ml-2"></i>');
        $.ajax({
            url: "/site/contrassenya-correcta",
            type: "post",
            dataType: "json",
            data: data
        }).always(() => {
            $('.fa-circle-notch').remove();
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

/**
 * Error labbel and type
 * @param tipo
 * @param missatge
 */
function mostrarLabelError(tipo, missatge) {
    if (tipo === "email") {
        $("#error-label-email")
            .html(missatge)
            .show("fast");
        setTimeout(function () {
            $("#error-label-email")
                .html(missatge)
                .hide("fast");
        }, 5000);
    }
    if (tipo === "password") {
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
