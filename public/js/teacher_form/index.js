(function ($, _, utils) {
    'use strict';

    (function () {
        var form = $('#app-form form');
        var messages = form.data('messages');
        utils.formMessages.render(form, messages);
    }());

    var proto = $.ui.autocomplete.prototype,
    initSource = proto._initSource;

    $("#select_school").autocomplete({
        source: window.location.protocol + "//" + window.location.host + "/teacher-form/mm",
        minLength: 4,
        select: function(event, ui) {
            console.log(ui);
            $("#mm_id").val(ui.item.mm_id);
            $("#el-school").val(ui.item.value);
            $("#el-schooltelef").val(ui.item.tel);
        },

        html: false,

        open: function(event, ui) {
            $(".ui-autocomplete").css("z-index", 1000);
        }
    });

}(window.jQuery, _, window.EDULABS.utils));



$(document).ready(function() {
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
   
    var x = 1; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div><div><label for="projects">Σχετική δράση που συμμετείχατε </label><input type="text" name="projects[]" id="projects"></div>        <div><label for="urlproject">URL δράσης που συμμετείχατε</label><input type="text" name="urlproject[]" id="urlproject"></div>        <div><label for "commentproject">Περιγραφή δράσης που συμμετείχατε</label><input type="text" name="commentsproject[]" id="commentproject"></div><a href="#" class="remove_field">Διαγραφή</a></div>'); 
        }
    });
   
    $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault();
         $(this).parent('div').remove(); x--;
    })


    $("#submit").click(function(){

       var projects = $("input[id='projects']")
              .map(function(){return $(this).val();}).get();
     
      var urlproject = $("input[id='urlproject']")
              .map(function(){return $(this).val();}).get();

     var commentproject = $("input[id='commentproject']")
              .map(function(){return $(this).val();}).get();

      var newfield = projects+"#"+urlproject+"#"+commentproject;

      $('#projectdescription').val(newfield);

    })



});