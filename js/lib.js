/*jslint browser: true, white: true, vars: true, plusplus: true, regexp: true, indent: 4, maxerr: 50 */
/*global $, jQuery*/

var hash = window.location.hash,
    supportsTouch = window.hasOwnProperty('ontouchstart') || window.navigator.msPointerEnabled ? true : false,
    TouchClickEvent = supportsTouch ? 'touchstart' : 'click';

/*
    equalHeight
    usage: equalHeight('selector');
*/
function equalHeight(group) {
    'use strict';
    var tallest = 0;
    $(group).height('').each(function() {
        var thisHeight = $(this).outerHeight();
        if(thisHeight > tallest) {
            tallest = thisHeight;
        }
    });
    $(group).height(tallest);
}

//return document
function screen(method){
    'use strict';
    if(method === 'height') {
        method = window.innerHeight || document.documentElement.clientHeight;
    } else if(method === 'width') {
        method = document.body.offsetWidth;
    } else {
        method = Math.max(document.documentElement.scrollTop, document.body.scrollTop);
    }
    return method;
}

//if mobile function
var mob = function(){
    'use strict';
    window.mobilecheck = function() {
        var check = false;
        (function(a){
            if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))){
                check = true;
            }
        }(navigator.userAgent||navigator.vendor||window.opera));
        return check;
    };
    var ua = navigator.userAgent,
        isIOS = /iPad/i.test(ua) || /iPhone OS 3_1_2/i.test(ua) || /iPhone OS 3_2_2/i.test(ua) || /iPhone/i.test(ua);

    if (window.mobilecheck() || isIOS) {
        return true;
    } else {
        return false;
    }
};

(function($) {
    'use strict';

//cssClassChanged trigger
    var originalAddClassMethod = jQuery.fn.addClass;
    jQuery.fn.addClass = function(){
        var result = originalAddClassMethod.apply( this, arguments );
        jQuery(this).trigger('cssClassChanged');
        return result;
    };

//find empty paragraphs
    $('p').each(function() {
        var t = $(this);
        if(t.html().replace(/\s|&nbsp;/g, '').length === 0) { t.addClass('jEmpty'); }
    });
}(jQuery));

function load_defer_img(source) {
    'use strict';
    return $.Deferred (function (task) {
        var image = new Image();
        image.onload = function () {task.resolve(image);};
        image.onerror = function () {task.reject();};
        image.src=source;
    }).promise();
}

function loadlater() {
    'use strict';
    $('[data-defer]').each(function(){
        var t = $(this),
            pre = t.data('pre'),
            img = t.data('defer');
        $.when(load_defer_img(img)).done(function (image) {
          t.removeAttr('data-defer');
            if(t.is('img')) {
              t.prop('src', img);
            } else {
              if(typeof pre !== 'undefined') {
                t.prepend(image);
              } else {
                t.append(image);
              }
            }
        });
    });
}

function is_numeric_input() {
    'use strict';
    $('.quantity input').each(function(){
        var t = $(this);
        if(t.parent().is('.input-number-box')) {
            return;
        }
        if(mob()) {
            t.attr('type', 'number').removeAttr('onkeyup');
        } else {
            t.attr('type', 'text').attr('autocomplete', 'off').attr('onkeyup', 'this.value = !isNaN(parseInt(this.value)) ? parseInt(this.value) : ""');
        }
        t.wrap('<div class="input-number-box" />').after('<i class="input-number-more" /><i class="input-number-less" />');
    });
}

window.onload = function () {
    'use strict';
    is_numeric_input();
    loadlater();
};
