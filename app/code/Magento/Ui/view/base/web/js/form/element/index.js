/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './abstract',
    './select',
    './multiple_select',
    './textarea',
    './boolean'
], function (Abstract, Select, MultipleSelect, Textarea, Bool) {
    'use strict';

    return {
        input:              Abstract,
        email:              Abstract,
        checkbox:           Bool,
        price:              Abstract,
        date:               Abstract,
        media:              Abstract,
        select:             Select,
        multiple_select:    MultipleSelect,
        textarea:           Textarea
    }
});