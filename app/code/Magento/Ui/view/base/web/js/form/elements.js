/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './element/input',
    './element/select',
    './element/fieldset'
], function (Input, Select, Fieldset) {
    'use strict';

    return {
        input: Input,
        select: Select,
        fieldset: Fieldset
    }
});