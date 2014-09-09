<!--
/**
 * {license_notice}
 *
 * @category    storage
 * @package     test
 * @copyright   {copyright}
 * @license     {license_link}
 */
-->
define([], function() {
    'use strict';

    function parseObject(name, value) {
        var key,
            result = [];

        for (key in value) {
            result.push(name + '[' + key + ']' + '=' + value[key])
        }

        return result.join('&');
    }

    function parseValue(name, value) {
        return name + '=' + value;
    }

    /**
     * Extracts sorting parameters from object and returns string representation of it.
     * @param {Object} param - Sorting parameters object, e.g. { field: 'field_to_sort', dir: 'asc' }.
     * @returns {String} Formatted string of type .
     * @private
     */
    function extractSortParams(params) {
        var result,
            sorting = params.sorting;

        if (typeof sorting === 'undefined') {
            return '';
        }

        result = '/sort/' + sorting.field + '/dir/' + sorting.direction;

        delete params.sorting;

        return result;
    }

    /**
     * Extracts pager parameters from an object and returns it's string representation.
     * @param {Object} param - .
     * @returns {String} Formatted string of type .
     * @private
     */
    function extractPagerParams(params) {
        var result,
            paging = params.paging;

        if (typeof paging === 'undefined') {
            return '';
        }

        result = '/limit/' + paging.pageSize + '/page/' + paging.current;

        delete params.paging;

        return result;
    }

    function formatFilter(filter) {
        var name = filter.field,
            value = filter.value;

        return typeof value !== 'object' ?
            parseValue(name, value) :
            parseObject(name, value);
    }

    function extractFilterParams(params) {
        var filters,
            result;

        filters = params.filter;

        if (typeof filters === 'undefined' || !filters.length) {
            return '';
        }

        result = filters.map(formatFilter).join('&');

        result = '/filter/' + btoa(encodeURI(result));

        delete params.filter;

        return result;
    }

    return function(root, params) {
        var url,
            lastChar;

        lastChar = root.charAt(root.length - 1);

        if (lastChar === '/') {
            root = root.substr(0, root.length - 1);
        }

        url =
            root +
            extractSortParams(params) +
            extractPagerParams(params) +
            extractFilterParams(params);

        return url;
    };

});