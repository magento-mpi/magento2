<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Logging\Model\Resource\Grid;

class Actions implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Logging\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Logging\Model\Resource\Event
     */
    protected $_resource;

    /**
     * @param \Magento\Logging\Helper\Data $loggingHelper
     * @param \Magento\Logging\Model\Resource\Event $resource
     */
    public function __construct(\Magento\Logging\Helper\Data $loggingHelper,
                                \Magento\Logging\Model\Resource\Event $resource)
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
