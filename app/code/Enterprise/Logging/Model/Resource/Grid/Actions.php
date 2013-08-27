<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Logging_Model_Resource_Grid_Actions implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Enterprise_Logging_Helper_Data
     */
    protected $_helper;

    /**
     * @var Enterprise_Logging_Model_Resource_Event
     */
    protected $_resource;

    /**
     * @param Enterprise_Logging_Helper_Data $loggingHelper
     * @param Enterprise_Logging_Model_Resource_Event $resource
     */
    public function __construct(Enterprise_Logging_Helper_Data $loggingHelper,
                                Enterprise_Logging_Model_Resource_Event $resource)
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
