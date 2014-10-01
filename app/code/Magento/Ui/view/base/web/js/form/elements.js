/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    './element/abstract',
    './element/select',
    './element/multiple_select'
], function (Abstract, Select, MultipleSelect) {
    'use strict';

    return {
        input:              Abstract,
        select:             Select,
        price:              Abstract,
        multiple_select:    MultipleSelect,
        email:              Abstract,
        checkbox:           Abstract
    }
});