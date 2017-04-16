/**
 * Created by Next on 24.02.2017.
 */

(function() {

    if(window.habb) {
        return;
    }

    window.habb = {};

    habb.formHelpers = {

        RequestDataToSelect: function (selectElement, type, sourceTrigger) {
            selectElement.prop("disabled", true);
            sourceTrigger.prop("disabled", true);

            selectElement.find('option').remove();
            var request = $.ajax({
                url: "/rest/ajax.php",
                data: {
                    "action": "select2.participants.get",
                    "type": type
                },
                type: "POST",
                success: function (data, textStatus) {
                    var result = data["result"];
                    var count = data["count"];
                    console.log("Received " + count + " items of array");
                    for (var i = 0; i < count; i++) {
                        var item = result[i];
                        selectElement.append("<option value='" + item["value"] + "'>" + item["text"] + "</option>")
                    }
                    selectElement.prop("disabled", false);
                    sourceTrigger.prop("disabled", false);

                },
                fail: function (data, textStatus) {
                    selectElement.prop("disabled", false);
                    sourceTrigger.prop("disabled", false);
                }
            });
        },

    };;

    habb.registrationHelpers = {
        phoneInput          : null,
        emailInput          : null,
        sbtBtn              : null,
        divPhone            : null,
        divEmail            : null,
        accountModalTitle   : null,
        accountModalBody    : null,
        vkInput             : null,
        dangerClass         : "uk-form-danger",
        successClass        : "uk-form-success",

        _markFields : function(field, statement) {

            if (statement == true) {

                if (field == "phone") {

                    this.phoneInput.addClass(this.dangerClass);
                    this.phoneInput.removeClass(this.successClass);

                    //this.divPhone.addClass("has-danger");
                    //this.divPhone.removeClass("has-success");


                } else if (field == "email") {
                    this.emailInput.addClass(this.dangerClass);
                    this.emailInput.removeClass(this.successClass);

                    //this.divEmail.addClass("has-danger");
                    //this.divEmail.removeClass("has-success");
                }
                this.sbtBtn.prop("disabled", true);


            } else {
                if (field == "phone") {

                    this.phoneInput.addClass(this.successClass);
                    this.phoneInput.removeClass(this.dangerClass);

                    //this.divPhone.addClass("has-success");
                    //this.divPhone.removeClass("has-danger");

                } else if (field == "email") {
                    this.emailInput.addClass(this.successClass);
                    this.emailInput.removeClass(this.dangerClass);

                    //this.divEmail.addClass("has-success");
                    //this.divEmail.removeClass("has-danger");
                }

                this.sbtBtn.prop("disabled", false);
            }
        },

        _searchValue : function(field, value) {
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            var url = "/ajax/search-gamer";
            var paramsData = {
                "_token" : CSRF_TOKEN,
                "field" : field,
                "value" : value
            };
            var request = $.ajax({
                url : url,
                data : paramsData,
                type : "POST",
                success : function(data, textStatus){

                    console.log(data);
                    var result = data["result"];
                    var exists = data["exists"];

                    habb.registrationHelpers._markFields(field, exists);
                    if (exists == false) return;
                    UIkit.modal('#accountModal').show();
                    //$('#accountModal').modal('show');
                }
            });
        },

        RegisterListeners : function () {

            this.phoneInput        = $('#phone');
            this.emailInput        = $('#email');
            this.sbtBtn            = $('#submit-btn');
            this.divPhone          = $('#divPhone');
            this.divEmail          = $('#divEmail');
            this.accountModalTitle = $('#accountModalTitle');
            this.accountModalBody  = $('#accountModalBody');
            this.vkInput           = $('#vk_page');

            this.vkInput.focus(function(){

                if ($(this).val() == "") {
                    $(this).val("https://vk.com/");
                }
            });

            this.vkInput.blur(function () {
                if ($(this).val() == "https://vk.com/") {
                    $(this).val("");
                }
            });

            $('#modalConfirmButton').on('click', function(){
                $('#inqured').prop('checked', true);
            });

            this.phoneInput.blur(function(){
                if ($(this).val() == "")  {

                    $(this).removeClass(habb.registrationHelpers.dangerClass);
                    $(this).removeClass(habb.registrationHelpers.successClass);
                    //registrationHelpers.divPhone.removeClass("has-danger");
                    //registrationHelpers.divPhone.removeClass("has-success");
                    return;
                }

                habb.registrationHelpers._searchValue("phone", $(this).val());
            });

            this.emailInput.blur(function(){

                if ($(this).val() == "")  {

                    $(this).removeClass(habb.registrationHelpers.dangerClass);
                    $(this).removeClass(habb.registrationHelpers.successClass);
                    //registrationHelpers.divEmail.removeClass("has-danger");
                    //registrationHelpers.divEmail.removeClass("has-success");
                    return;
                }
                habb.registrationHelpers._searchValue("email", $(this).val());
            });
        },

    };

    habb.httpHelpers = {
        /**
         * Отправляет данные аяксом
         * @param url - адрес отправки
         * @param data - данные
         * @param onSuccess - коллбэк в случае успеха
         * @param onError - коллбек в случае ошибки
         * @constructor
         */
        AjaxRequest : function (url, data, onSuccess, onError) {

            onError = typeof onError !== 'undefined' ? onError : null;
            var request = $.ajax({
                url : url,
                type: 'post',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data : data,
                success  : onSuccess,
                error : onError
            });
            //request.send();
        }
    };

    habb.ckEditorHelpers = {};

    habb.tournamentHelper = {};

}());



