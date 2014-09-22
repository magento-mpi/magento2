/**
 * Finds all comment elements
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/*jshint browser:true jquery:true expr:true*/
define([
    "jquery"
], function($){
    "use strict";
    $.fn.comments = function () {
        var elements = [];
        var lookup = function (el) {
            if (el.is('iframe')) {
                var hostName = window.location.hostname,
                    iFrameHostName = $('<a>').prop('href', el.prop('src')).prop('hostname');
                if (hostName != iFrameHostName) {
                    return elements;
                }
            }
            el.contents().each(function (i, el) {
                if (el.nodeType == 8) {
                    elements.push(el);
                } else if (el.nodeType == 1) {
                    lookup($(el));
                }
            });
        };
        lookup(this);
        return elements;
    };

});