<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_AdminNotification_Block_System_Messages extends Magento_Backend_Block_Template
{
    /**
     * Message list
     *
     * @var Magento_AdminNotification_Model_Resource_System_Message_Collection_Synchronized
     */
    protected $_messages;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param
     * Magento_AdminNotification_Model_Resource_System_Message_Collection_Synchronized
     * $messages
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_AdminNotification_Model_Resource_System_Message_Collection_Synchronized $messages,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_messages = $messages;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (count($this->_messages->getItems())) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Retrieve message list
     *
     * @return Magento_AdminNotification_Model_System_MessageInterface[]
     */
    public function getLastCritical()
    {
        $items = array_values($this->_messages->getItems());
        if (isset($items[0]) && $items[0]->getSeverity()
            == Magento_AdminNotification_Model_System_MessageInterface::SEVERITY_CRITICAL
        ) {
            return $items[0];
        }
        return null;
    }

    /**
     * Retrieve number of critical messages
     *
     * @return int
     */
    public function getCriticalCount()
    {
        return $this->_messages->getCountBySeverity(
            Magento_AdminNotification_Model_System_MessageInterface::SEVERITY_CRITICAL
        );
    }

    /**
     * Retrieve number of major messages
     *
     * @return int
     */
    public function getMajorCount()
    {
        return $this->_messages->getCountBySeverity(
            Magento_AdminNotification_Model_System_MessageInterface::SEVERITY_MAJOR
        );
    }

    /**
     * Check whether system messages are present
     *
     * @return bool
     */
    public function hasMessages()
    {
        return (bool) count($this->_messages->getItems());
    }

    /**
     * Retrieve message list url
     *
     * @return string
     */
    protected function _getMessagesUrl()
    {
        return $this->getUrl('adminhtml/system_message/list');
    }

    /**
     * Initialize Syste,Message dialog widget
     *
     * @return string
     */
    public function getSystemMessageDialogJson()
    {
        return $this->_coreData->jsonEncode(array(
            'systemMessageDialog' => array(
                'autoOpen' => false,
                'width' => 600,
                'ajaxUrl' => $this->_getMessagesUrl(),
            ),
        ));
    }
}
