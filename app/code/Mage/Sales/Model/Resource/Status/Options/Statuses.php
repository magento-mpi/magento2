<?php
/**
 * Order statuses option array
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Sales_Model_Resource_Status_Options_Statuses implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * @var Mage_Sales_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Sales_Helper_Data $helper
     */
    public function __construct(Mage_Sales_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Return options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '0' => $this->_helper->__('No'),
            '1' => $this->_helper->__('Yes'),
        );
    }
}