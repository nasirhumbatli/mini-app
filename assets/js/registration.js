$('#registrationForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: 'register.php',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json'
    }).done(function (res) {
        $('#formAlert').removeClass('d-none alert-danger').addClass('alert alert-success').text(res.message);
        $('#registrationForm')[0].reset();
        $('#fullNameError').text('');
        $('#emailError').text('');
        $('#companyError').text('');
    }).fail(function (responseJson) {
        let res = {};
        res = JSON.parse(responseJson.responseText);
        $('#formAlert').removeClass('d-none alert-success').addClass('alert alert-danger').text(res.message);

        if (res.fields) {
            if (res.fields.full_name) $('#fullNameError').text(res.fields.full_name);
            if (res.fields.email) $('#emailError').text(res.fields.email);
            if (res.fields.company) $('#companyError').text(res.fields.company);
        }
    });
});
