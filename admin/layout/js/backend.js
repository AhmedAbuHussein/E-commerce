/**
 * global $ , alert , document, window
 */

$(function() {
    'use strict';

    $('.show-panel').click(function() {
        $(this).toggleClass("selected").parent().next('.panel-body').slideToggle(500);
        if ($(this).hasClass("selected")) {
            $(this).html("<i class='fa fa-minus fa-lg'></i>");
        } else {
            $(this).html("<i class='fa fa-plus fa-lg'></i>");
        }
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

    $('.cat-box h3').click(function() {
        if ($(this).children('i').hasClass('fa-caret-right')) {
            $(this).children('i').removeClass('fa-caret-right').addClass('fa-caret-down');
        } else {
            $(this).children('i').removeClass('fa-caret-down').addClass('fa-caret-right');
        }
        $(this).next('.cat-info').slideToggle(500);
    })

});