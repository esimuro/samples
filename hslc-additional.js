
// Additional input validations for HSLC specific types

	$.validator.addMethod("money", function(value, element) {
        return this.optional(element) || /^(\d{1,3})(\.\d{1,2})?$/.test(value);
    }, "Please enter dollars and cents only, format 0.99");

    $.validator.addMethod("inputselect", function(value, element) {      
        if (element.value == "Null")  {
            return false;
        } else {
            return true;
        }
    }, "Please select an option."  );
	$.validator.addMethod("zipUS", function(value, element) {
        return this.optional(element) || /^\d{5}(-\d{4})?$/.test(value);
    }, "Please enter a valid US zip code");
	$.validator.addMethod("realCCV", function(value, element, param) {
        var target = $(param);
        if (target.val() == 'A0' ) {
            return this.optional(element) || /^\d{4}?$/.test(value); 
        } else {
            return this.optional(element) || /^\d{3}?$/.test(value);
        }
    }, "The CCV code provided is not valid for this card type");
