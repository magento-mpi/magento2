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
    './textarea'
], function (Abstract, Select, MultipleSelect, Textarea) {
    'use strict';

    return {
        input:              Abstract,
        email:              Abstract,
        checkbox:           Abstract,
        price:              Abstract,
        date:               Abstract,
        media:              Abstract,
        select:             Select,
        multiple_select:    MultipleSelect,
        textarea:           Textarea
    }
});