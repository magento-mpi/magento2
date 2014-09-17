/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** Assembles available filter controls and returns it's mapping. */
define([
    './item/filter_input',
    './item/filter_select',
    './item/filter_range'
], function (InputControl, SelectControl, RangeControl) {
    'use strict';

    return {
        filter_input: InputControl,
        filter_select: SelectControl,
        filter_range: RangeControl
    }
});