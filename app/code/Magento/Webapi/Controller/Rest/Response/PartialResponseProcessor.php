<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Webapi\Controller\Rest\Response;

/**
 * Class to handle partial service response
 */
class PartialResponseProcessor
{
    const FILTER_PARAMETER = 'fields';

    /**
     * Process filter from the request and apply over response to get the partial results
     *
     * @param string $filter filter criteria for partial response filtering
     * @param array $response
     * @return array partial response array or empty array if invalid filter criteria is provided
     */
    public function filter($filter, $response)
    {
        $filterArray = $this->parse($filter);
        if (is_null($filterArray)) {
            return [];
        }
        $partialResponse = $this->applyFilter($response, $filterArray);
        return $partialResponse;
    }


    /**
     * Parse filter string into associative array. Field names are returned as keys with values for scalar fields as 1.
     *
     * @param string $filterString
     * <pre>
     *  ex. customer[id,email],addresses[city,postcode,region[region_code,region]]
     * </pre>
     * @return array|null
     * <pre>
     *  ex.
     * array(
     *      'customer' =>
     *           array(
     *               'id' => 1,
     *               'email' => 1,
     *               ),
     *      'addresses' =>
     *           array(
     *               'city' => 1,
     *               'postcode' => 1,
     *                   'region' =>
     *                       array(
     *                           'region_code' => 1,
     *                           'region' => 1,
     *                         ),
     *               ),
     *      )
     * </pre>
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function parse($filterString)
    {
        $length = strlen($filterString);
        //Permissible characters in filter string: letter, number, underscore, square brackets and comma
        if ($length == 0 || preg_match('/[^\w\[\],]+/', $filterString)) {
            return null;
        }

        $start = null;
        $current = [];
        $stack = [];
        $parent = [];
        $currentElement = null;

        for ($position = 0; $position < $length; $position++) {
            //Extracting field when encountering field separators
            if (in_array($filterString[$position], ['[', ']', ','])) {
                if ($start !== null) {
                    $currentElement = substr($filterString, $start, $position - $start);
                    $current[$currentElement] = 1;
                }
                $start = null;
            }
            switch ($filterString[$position]) {
                case '[':
                    array_push($parent, $currentElement);
                    // push current field in stack and initialize current
                    array_push($stack, $current);
                    $current = [];
                    break;

                case ']':
                    //cache current
                    $temp = $current;
                    //Initialize with previous
                    $current = array_pop($stack);
                    //Add from cache
                    $current[array_pop($parent)] = $temp;
                    break;

                //Do nothing on comma. On the next iteration field will be extracted
                case ',':
                    break;

                default:
                    //Move position if no field separators found
                    if ($start === null) {
                        $start = $position;
                    }
            }
        }
        //Check for wrongly formatted filter
        if (!empty($stack)) {
            return null;
        }
        //Check if there's any field remaining that's not added to response
        if ($start !== null) {
            $currentElement = substr($filterString, $start, $position - $start);
            $current[$currentElement] = 1;
        }
        return $current;
    }

    /**
     * Apply filter array
     *
     * @param array $responseArray
     * @param array $filter
     * @return array
     */
    protected function applyFilter(array $responseArray, array $filter)
    {
        $arrayIntersect = null;
        //Check if its a sequential array. Presence of sequential arrays mean that the filed is a collection
        //and the filtering will be applied to all the collection items
        if (!(bool)count(array_filter(array_keys($responseArray), 'is_string'))) {
            foreach ($responseArray as $key => &$item) {
                $arrayIntersect[$key] = $this->recursiveArrayIntersectKey($item, $filter);
            }
        } else {
            $arrayIntersect = $this->recursiveArrayIntersectKey($responseArray, $filter);
        }
        return $arrayIntersect;
    }

    /**
     * Recursively compute intersection of response and filter arrays
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected function recursiveArrayIntersectKey(array $array1, array $array2)
    {
        //If the field in array2 (filter) is not present in array1 (response) it will be removed after intersect
        $arrayIntersect = array_intersect_key($array1, $array2);
        foreach ($arrayIntersect as $key => &$value) {
            if (is_array($value) && is_array($array2[$key])) {
                $value = $this->applyFilter($value, $array2[$key]);
            }
        }
        return $arrayIntersect;
    }

    /**
     * Extract the value for partial response filtering from request
     *
     * @param \Zend_Controller_Request_Http $request
     * @return string
     */
    public function extractFilter(\Zend_Controller_Request_Http $request)
    {
        $params = $request->getParams();
        if (isset($params[self::FILTER_PARAMETER])) {
            return $params[self::FILTER_PARAMETER];
        }
        return null;
    }
}