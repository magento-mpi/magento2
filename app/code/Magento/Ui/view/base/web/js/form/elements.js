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
    './element/multiple_select',
    './element/email'
], function (Input, Select, Price, MultipleSelect, Email) {
    'use strict';

    return {
        input: Input,
        select: Select,
        price: Price,
        multiple_select: MultipleSelect,
        email: Email
    }
});