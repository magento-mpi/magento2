/**
 * {license_notice}
 *
 * @category    mage
 * @package     mage
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true */
;(function($, document) {
    'use strict';
    var ESC_KEY_CODE = '27';

    $(document).on('click.dropdown', function(event) {
        var $target = $(event.target);
        if (!$target.is('[data-toggle="dropdown"].active, [data-toggle="dropdown"].active *, ' +
           '[data-toggle="dropdown"].active + .dropdown-menu, [data-toggle="dropdown"].active + .dropdown-menu *,' +
           '[data-toggle="dropdown"].active + [data-target="dropdown"],' +
           '[data-toggle="dropdown"].active + [data-target="dropdown"] *')
        ) {
            $('[data-toggle="dropdown"].active').trigger('close.dropdown');
        }
    });
    $(document).on('keyup.dropdown', function(event) {
        if (event.keyCode == ESC_KEY_CODE) {
            $('[data-toggle="dropdown"].active').trigger('close.dropdown');
        }
    });

    $.fn.dropdown = function(options) {
        options = $.extend({
            parent: null,
            btnArrow: '.arrow',
            activeClass: 'active'
        }, options);

        return this.each(function() {
            var elem = $(this);
            //following line failed jslint
            //parent = elem.parent();
                //menu = $('[data-target="dropdown"]', parent) || $('.dropdown-menu', parent);

            elem.on('open.dropdown', function() {
                elem
                    .addClass(options.activeClass)
                    .parent()
                    .addClass(options.activeClass);
                elem.find(options.btnArrow).text('\u25b2'); // arrow up
            });

            elem.on('close.dropdown', function() {
                elem
                    .removeClass(options.activeClass)
                    .parent()
                    .removeClass(options.activeClass);
                elem.find(options.btnArrow).text('\u25bc'); // arrow down
            });

            elem.on('click.dropdown', function() {
                elem.trigger(elem.hasClass('active') ? 'close.dropdown' : 'open.dropdown');
                return false;
            });
        });
    };

    $(document).ready(function() {
        $('[data-toggle="dropdown"]').dropdown();
    });
})(window.jQuery, document);
