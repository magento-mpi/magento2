/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** Assembles available input controls and returns it's mapping. */
define([
    './controls/input',
    './controls/select',
    './controls/date'
], function (InputControl, SelectControl, DateControl) {
    'use strict';

    return {
        input:  InputControl,
        select: SelectControl,
        date:   DateControl
    }
});