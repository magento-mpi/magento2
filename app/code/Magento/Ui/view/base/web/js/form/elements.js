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
    './element/email'
], function (Input, Select, Price, Email) {
    'use strict';

    return {
        input: Input,
        select: Select,
        price: Price,
        email: Email
    }
});