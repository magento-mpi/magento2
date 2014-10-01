/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './input',
    './select',
    './multiple_select',
    './textarea'
], function (Input, Select, MultipleSelect, Textarea) {
    'use strict';

    return {
        input:              Input,
        price:              Input,
        email:              Input,
        textarea:           Textarea,
        select:             Select,
        multiple_select:    MultipleSelect
    }
});