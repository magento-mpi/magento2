/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './element/input',
    './element/select',
    './element/price'
], function (Input, Select, Price) {
    'use strict';

    return {
        input: Input,
        select: Select,
        price: Price
    }
});