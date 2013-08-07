<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Convert
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Default converter for Magento_Objects to arrays
 *
 * @category   Magento
 * @package    Magento_Convert
 * @author     Magento Extensibility Team <DL-X-Extensibility-Team@corp.ebay.com>
 */
class Magento_Convert_Object
{
    /** Constant used to mark cycles in the input array/objects */
    const CYCLE_DETECTED_MARK = '*** CYCLE DETECTED ***';

    /**
     * Convert input data into an array and return the resulting array.
     * The resulting array should not contain any objects.
     *
     * @param mixed $data input data
     * @return array Data converted to an array
     */
    public function convertDataToArray($data)
    {
        $result = array();
        foreach ($data as $key => $value) {
            if (is_object($value) || is_array($value)) {
                $result[$key] = $this->_convertObjectToArray($value);
            } else {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    /**
     * Converts a Magento_Object into an array, including any children objects
     *
     * @param mixed $obj array or object to convert
     * @param array $objects array of object hashes used for cycle detection
     * @return array|string Converted object or CYCLE_DETECTED_MARK
     */
    protected function _convertObjectToArray($obj, &$objects = array())
    {
        $data = array();
        if (is_object($obj)) {
            $hash = spl_object_hash($obj);
            if (!empty($objects[$hash])) {
                return self::CYCLE_DETECTED_MARK;
            }
            $objects[$hash] = true;
            if ($obj instanceof Magento_Object) {
                $data = $obj->getData();
            } else {
                $data = (array)$obj;
            }
        } else if (is_array($obj)) {
            $data = $obj;
        }

        $result = array();
        foreach ($data as $key => $value) {
            if (is_scalar($value)) {
                $result[$key] = $value;
            } else if (is_array($value)) {
                $result[$key] = $this->_convertObjectToArray($value, $objects);
            } else if ($value instanceof Magento_Object) {
                $result[$key] = $this->_convertObjectToArray($value, $objects);
            }
        }
        return $result;
    }

}
