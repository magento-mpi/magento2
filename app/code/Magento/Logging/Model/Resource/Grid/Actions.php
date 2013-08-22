<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Logging_Model_Resource_Grid_Actions implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Logging_Helper_Data
     */
    protected $_helper;

    /**
     * @var Magento_Logging_Model_Resource_Event
     */
    protected $_resource;

    /**
     * @param Magento_Logging_Helper_Data $loggingHelper
     * @param Magento_Logging_Model_Resource_Event $resource
     */
    public function __construct(Magento_Logging_Helper_Data $loggingHelper,
                                Magento_Logging_Model_Resource_Event $resource)
    {
        $this->_helper = $loggingHelper;
        $this->_resource = $resource;
    }

    public function toOptionArray()
    {
        $actions = array();
        $fieldValues = $this->_resource->getAllFieldValues('action');

        foreach ($fieldValues as $action) {
            $actions[$action] = $this->_helper->getLoggingActionTranslatedLabel($action);
        }
        return $actions;
    }
}
