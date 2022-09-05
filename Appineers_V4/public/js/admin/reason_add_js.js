/** users_management module script */
Project.modules.reason_management = {
    init: function() {
        
        valid_more_elements = [];
        
        
    },
    validate: function (){
        
        $("#frmaddupdate").validate({
            onfocusout: false,
            ignore:".ignore-valid, .ignore-show-hide",
            rules : {
            "i_reason_name": {
                "required": true
            },
            "i_reason_entity_type": {
                "required": true
            },
            "i_reason_status": {
                "required": true
            }
        },
            messages : {
            "i_reason_name": {
                "required": ci_js_validation_message(js_lang_label.GENERIC_PLEASE_ENTER_A_VALUE_FOR_THE__C35FIELD_C35_FIELD_C46 ,"#FIELD#",js_lang_label.REASON_MANAGEMENT_NAME)
            },
            "i_reason_entity_type": {
                "required": ci_js_validation_message(js_lang_label.GENERIC_PLEASE_ENTER_A_VALUE_FOR_THE__C35FIELD_C35_FIELD_C46 ,"#FIELD#",js_lang_label.REASON_MANAGEMENT_ENTITY_TYPE)
            },
            "i_reason_status": {
                "required": ci_js_validation_message(js_lang_label.GENERIC_PLEASE_ENTER_A_VALUE_FOR_THE__C35FIELD_C35_FIELD_C46 ,"#FIELD#",js_lang_label.REASON_MANAGEMENT_STATUS)
            }
        },
            errorPlacement : function(error, element) {
                switch(element.attr("name")){
                        case 'i_reason_name':
                            $('#'+element.attr('id')+'Err').html(error);
                            break;
                    
                        case 'i_reason_entity_type':
                            $('#'+element.attr('id')+'Err').html(error);
                            break;
                
                        case 'i_reason_status':
                            $('#'+element.attr('id')+'Err').html(error);
                            break;
                    default:
                        printErrorMessage(element, valid_more_elements, error);
                        break;
                }
                
            },
            invalidHandler: function(form, validator) {
                var errors = validator.numberOfInvalids();
                if (errors) {                    
                    validator.errorList[0].element.focus();
                }
            },
            submitHandler: function (form) {
                getAdminFormValidate();
                return false;
            }
        });
        
    },
    callEvents: function() {
        this.validate();
        this.initEvents();
        this.toggleEvents();
        callGoogleMapEvents();
        
    },
    callChilds: function(){
        
        callGoogleMapEvents();
    },
    initEvents: function(elem){
        
            
    },
    childEvents: function(elem, eleObj){
        
    },
    toggleEvents: function(){
        
    },
    dropdownLayouts:function(elem){
        
    }
}
Project.modules.reason_management.init();
