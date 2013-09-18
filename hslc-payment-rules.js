$().ready(function() {
    // validate payFines form on keyup and submit
    $("#payment_form").validate({
        errorClass: "invalid",
        errorContainer: "#messageBox",
        errorLabelContainer: "#messageBox ul",
        wrapper: "li",  
        rules: {
            cardType: {
                required: true,
                inputselect: true
            },
            cardNumber: {
                required: true,
                creditcard: true
            },
            'card-ccv': {
                required: true,
                integer:  true,
                minlength: 3,
                maxlength: 4,
                realCCV: "#cardType"
            },
            'card-name': {
                required: true,
                minlength: 5,
                maxlength: 100
            },
            addressLine1:   {
                required: true,
                minlength: 5,
                maxlength: 100
            },
            addressCity:   {
                required: true,
                minlength: 5,
                maxlength: 100
            },
            addressState:  {
                required: true,
                inputselect: true
            },
            addressZip:  {
                required: true,
                zipUS:    true
            },
            addressEmail: {
                required: true,
                email: true
            },
 
        },
        messages: {
            cardType:  "You must select a Credit Card type to pay fines",
            cardNumber:  {
                required: "Credit card number is required",
                creditcard: "Please provide a valid credit card number"
            },
            'card-ccv':  {
                required:  "CCV number is required",
                minlength: "CCV number must be 3 or 4 digets",
                maxlength: "CCV number must be 3 or 4 digets"
            },
            'card-name': {
                required: "Cardholders name is required"
            },
            addressLine1:   {
                required: "Cardholders address is required"
            },
            addressCity:   {
                required: "Cardholders city is required"
            },
            addressState: "Cardholders state is required",
            addressZip:  {
                required: "Cardholders zipcode is required"
            },
            addressEmail: {
                required:  "Cardholders email address is required",
                email:  "A valid email address is required"
            }
        }
    })
});
