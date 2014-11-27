/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** Assembles available filter controls and returns it's mapping. */
define([
    './input',
    './select',
    './range'
], function (InputControl, SelectControl, RangeControl) {
    'use strict';

    return {
        input:      InputControl,
        select:     SelectControl,
        date:       RangeControl,
        range:      RangeControl,
        store:      SelectControl
    }
});