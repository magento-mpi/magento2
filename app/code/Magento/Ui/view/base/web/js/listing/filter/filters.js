/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** Assembles available filter controls and returns it's mapping. */
define([
    './item/input',
    './item/select',
    './item/range',
    './item/store'
], function (InputControl, SelectControl, RangeControl, StoreControl) {
    'use strict';

    return {
        input:      InputControl,
        select:     SelectControl,
        date:       RangeControl,
        range:      RangeControl,
        store:      StoreControl
    }
});