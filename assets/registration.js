$('#registrationForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
        url: 'insert.php',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json'
    }).done(function (res) {
        $('#formAlert').removeClass('d-none alert-danger').addClass('alert alert-success').text(res.message);
        $('#registrationForm')[0].reset();
    }).fail(function (xhr) {
        let res = {};
        try {
            res = JSON.parse(xhr.responseText)
        } catch {
        }
        $('#formAlert').removeClass('d-none alert-success').addClass('alert alert-danger').text(res.message);

        if (res.fields) {
            if (res.fields.full_name) $('#fullNameError').text(res.fields.full_name);
            if (res.fields.email) $('#emailError').text(res.fields.email);
            if (res.fields.company) $('#companyError').text(res.fields.company);
        }
    });
});
