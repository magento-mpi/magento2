define(['_'], function (_) {

    /**
     * Extracts sorting parameters from object and returns string representation of it.
     * @param {Object} param - Sorting parameters object, e.g. { field: 'field_to_sort', dir: 'asc' }.
     * @returns {String} Formatted string of type .
     * @private
     */

    function extractSortParams(params) {
        params = params.sorting;

        return '/sort/' + params.field + '/dir/' + params.direction;
    }

    /**
     * Extracts pager parameters from an object and returns it's string representation.
     * @param {Object} param - .
     * @returns {String} Formatted string of type .
     * @private
     */

    function extractPagerParams(params) {
        params = params.paging;

        return '/limit/' + params.pageSize + '/page/' + params.current;
    }

    return {

        getFor: function (root, params) {
            var url = '';

            if (root) {
                url = root;

                url += extractSortParams(params);
                url += extractPagerParams(params);
            }

            return {
                url: url,
                data: { form_key: FORM_KEY }
            };
        }
    }
});