<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Logging_Model_Resource_Grid_Statuses implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Logging_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_Logging_Helper_Data $loggingHelper
     */
    public function __construct(Magento_Logging_Helper_Data $loggingHelper)
    {
        $this->_helper = $loggingHelper;
    }

    public function toOptionArray()
    {
        return array(
            Magento_Logging_Model_Event::RESULT_SUCCESS => $this->_helper->__('Success'),
            Magento_Logging_Model_Event::RESULT_FAILURE => $this->_helper->__('Failure'),
        );
    }
}
