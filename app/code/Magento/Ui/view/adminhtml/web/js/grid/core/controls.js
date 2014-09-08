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