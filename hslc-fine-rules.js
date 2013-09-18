$().ready(function() {
    // validate payFines form on keyup and submit
    $("#payFines").validate({
        rules: {
            'payAmount[]': {
                required: true,
                money: true
            }
        },
        messages: {
            amount: "Please enter US currency only, format 0.00",
        }
    })
});
