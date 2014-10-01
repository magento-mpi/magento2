/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './element/input',
    './element/select',
    './element/price',
    './element/multiple_select'
], function (Input, Select, Price, MultipleSelect) {
    'use strict';

    return {
        input: Input,
        select: Select,
        price: Price,
        multiple_select: MultipleSelect
    }
});