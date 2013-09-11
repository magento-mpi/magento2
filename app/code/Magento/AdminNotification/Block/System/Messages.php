<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\AdminNotification\Block\System;

class Messages extends \Magento\Backend\Block\Template
{
    /**
     * Message list
     *
     * @var \Magento\AdminNotification\Model\Resource\System\Message\Collection\Synchronized
     */
    protected $_messages;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\AdminNotification\Model\Resource\System\Message\Collection\Synchronized $messages
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\AdminNotification\Model\Resource\System\Message\Collection\Synchronized $messages,
        array $data = array()
    ) {
        parent::__construct($context, $data);
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
     * @return \Magento\AdminNotification\Model\System\MessageInterface[]
     */
    public function getLastCritical()
    {
        $items = array_values($this->_messages->getItems());
        if (isset($items[0]) && $items[0]->getSeverity()
            == \Magento\AdminNotification\Model\System\MessageInterface::SEVERITY_CRITICAL
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
            \Magento\AdminNotification\Model\System\MessageInterface::SEVERITY_CRITICAL
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
            \Magento\AdminNotification\Model\System\MessageInterface::SEVERITY_MAJOR
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
        return $this->helper('Magento\Core\Helper\Data')->jsonEncode(array(
            'systemMessageDialog' => array(
                'autoOpen' => false,
                'width' => 600,
                'ajaxUrl' => $this->_getMessagesUrl(),
            ),
        ));
    }
}
