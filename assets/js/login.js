$('#loginForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: 'login_authentication.php',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json'
    }).done(function (res) {
        window.location.href = res.data.redirect;
    }).fail(function (responseJson) {
        let res = {};
        res = JSON.parse(responseJson.responseText);
        $('#formAlert').removeClass('d-none alert-success').addClass('alert alert-danger').text(res.message);

        if (res.fields) {
            if (res.fields.email) $('#emailError').text(res.fields.email);
            if (res.fields.password) $('#passwordError').text(res.fields.password);
        }
    });
});