define([
    '_'
], function (_) {

    function parseObject( name, value ){
        var keys    = Object.keys(value),
            len     = keys.length,
            result  = '';

        keys.forEach(function(key, i){
            result += name +'['+ key +']' + '=' + value[key];

            if( i < len - 1){
                result += '&';
            }
        });

        return result;
    }

    function parseValue(name, value){
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

        result = '/sort/' + sorting.field + '/dir/' + sorting.direction;;

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

        result = '/limit/' + paging.pageSize + '/page/' + paging.current;

        delete params.paging;

        return result;
    }

    function extractFilterParams(params){
        var filters,
            len,
            result,
            name,
            value;

        filters = params.filter;

        if( typeof filters === 'undefined' || !filters.length ){
            return '';
        }

        len     = filters.length,
        result  = '/filter/';

        fiters.forEach(function( filter, i ){
            name    = filter.field;
            value   = filter.value;

            result += typeof value !== 'object' ? 
                parseValue(name, value) :
                parseObject(name, value);

            if( i < len - 1 ){
                result += '&';
            }
        });

        delete params.filter;

        return result;
    }

    return function (root, params) {
        var url = '',
            lastChar;

        lastChar = root.charAt(root.length-1);
        
        if( lastChar === '/' ){
            root = root.substr(0, root.length-1);
        }
        
        url +=
            root +
            extractSortParams(params) +
            extractPagerParams(params) +
            extractFilterParams(params);

        return url;
    };
    
});