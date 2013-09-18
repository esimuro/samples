$().ready(function() {
    // validate payFines form on keyup and submit
    $("#payment_form").validate({
        errorClass: "invalid",
        errorContainer: "#messageBox",
        errorLabelContainer: "#messageBox ul",
        wrapper: "li",  
        rules: {
            'newPass2': "#newPass1",
            'current': {
                required: true
            },
            'newPass1': {
                required: true
            },
            'newPass2': {
            	required: true,
                equalTo: "#newPass1"
            } 
        },
        messages: {
           'current': "You must provide your current password",
            'newPass1':  "A new password is required to continue",
            'newPass2':  {
                required: "A new password is required to continue",
                equalTo: "Please enter the same password as above"
            } 
        }
    })
});
