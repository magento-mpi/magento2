/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract',
    './select',
    './textarea',
    './boolean'
], function (Abstract, Select, Textarea, Bool) {
    'use strict';

    return {
        input:              Abstract,
        email:              Abstract,
        checkbox:           Bool,
        price:              Abstract,
        date:               Abstract,
        media:              Abstract,
        select:             Select,
        multiple_select:    Select,
        textarea:           Textarea
    }
});