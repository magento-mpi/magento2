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
], function (Input, Select, MultipleSelect) {
    'use strict';

    return {
        input:              Input,
        price:              Input,
        email:              Input,
        textarea:           Input,
        select:             Select,
        multiple_select:    MultipleSelect
    }
});