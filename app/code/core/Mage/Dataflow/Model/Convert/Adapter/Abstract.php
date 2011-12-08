<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Convert abstract adapter
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Dataflow_Model_Convert_Adapter_Abstract
    extends Mage_Dataflow_Model_Convert_Container_Abstract
    implements Mage_Dataflow_Model_Convert_Adapter_Interface
{

    /**
     * Adapter resource instance
     *
     * @var object
     */
    protected $_resource;

    /**
     * Retrieve resource generic method
     *
     * @return object
     */
    public function getResource()
    {
        return $this->_resource;
    }

    /**
     * Set resource for the adapter
     *
     * @param object $resource
     * @return Mage_Dataflow_Model_Convert_Adapter_Abstract
     */
    public function setResource($resource)
    {
        $this->_resource = $resource;
        return $this;
    }

    public function getNumber($value)
    {
        if (!($separator = $this->getBatchParams('decimal_separator'))) {
            $separator = '.';
        }

        $allow  = array('0',1,2,3,4,5,6,7,8,9,'-',$separator);

        $number = '';
        for ($i = 0; $i < strlen($value); $i ++) {
            if (in_array($value[$i], $allow)) {
                $number .= $value[$i];
            }
        }

        if ($separator != '.') {
            $number = str_replace($separator, '.', $number);
        }

        return floatval($number);
    }
}
