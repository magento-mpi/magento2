define([
    './controls/input',
    './controls/select',
    './controls/date'
], function (InputControl, SelectControl, DateControl) {
    return {
        input:  InputControl,
        select: SelectControl,
        date:   DateControl
    }
});