/**
 * global $ , alert , document, window
 */

$(function() {
    'use strict';


    $("#gallery").mixItUp();

    $('.onChange').change(function(){
        $('.changed').fadeIn();        
    });



    $('.panel-heading i:first-child').click(function() {
        $(this).parent('.panel-heading').next('.panel-body').slideToggle(500);
        if($(this).hasClass('fa-chevron-down')){
            $(this).removeClass('fa-chevron-down').addClass('fa-chevron-right');
        } else{
            $(this).removeClass('fa-chevron-right').addClass('fa-chevron-down');
        }
    });


    $(".log-page h2 span").each(function() {
        if ($(this).hasClass('selected')) {
            $(".log-page form").hide();
            $(".log-page form." + $(this).data('class')).show();
            console.log($(".log-page form." + $(this).data('class')));
        }
    });

    $(".log-page h2 span").click(function() {
        $(this).addClass('selected').siblings().removeClass('selected');
        $(".log-page form").hide();
        $("." + $(this).data('class')).show(350);

        $('.form-alert').hide();
    });

    $('select').selectBoxIt({
        autoWidth: false
    });

    $('[placeholder]').focus(function() {
        $(this).attr('data-text', $(this).attr('placeholder'));
        $(this).attr('placeholder', '');
    }).blur(function() {
        $(this).attr('placeholder', $(this).attr('data-text'));
    });

    $('input').each(function() {
        if ($(this).attr('required') === "required") {
            $(this).after("<span class='require'>*</span>")
        }
    })

    var pass = $('.password');
    $('.show-pass').hover(function() {
        pass.attr('type', 'text');
    }, function() {
        pass.attr('type', 'password');
    })

    $('.confirm').click(function() {
        return confirm("Are you sure ?")
    });

    $('.form-ads input').each(function () {

        $(this).keyup(function () {

            if($(this).data('class') == 'price'){
                $( ".preview-" + $(this).data('class')).text("$" + $(this).val());    
            }else{
                $( ".preview-" + $(this).data('class')).text($(this).val());
            }
        });

    });


    function readURL(input,$seleector) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $($seleector).attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
      }
    }

    $("input[type='file']").change(function() {
      readURL(this,'.preview');
    });

});