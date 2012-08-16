<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Xmlconnect queue edit block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Queue_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_objectId    = 'id';
        $this->_controller  = 'adminhtml_queue';
        $this->_blockGroup  = 'Mage_XmlConnect';
        parent::_construct();

        $message = Mage::registry('current_message');
        if ($message && $message->getStatus() != Mage_XmlConnect_Model_Queue::STATUS_IN_QUEUE) {
            $this->_removeButton('reset');
            $this->_removeButton('save');
        } else {
            $this->_updateButton('save', 'label', $this->__('Queue Message'));
            $this->_updateButton('save', 'onclick', 'if (editForm.submit()) {disableElements(\'save\')}');
        }
        $this->_removeButton('delete');

        $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getBackUrl() . '\')');
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        $template = Mage::registry('current_template');
        $message  = Mage::registry('current_message');
        return $message && !$message->getId() && $template && $template->getId()
            ? $this->getUrl('*/*/template')
            : $this->getUrl('*/*/queue');
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $message = Mage::registry('current_message');
        if ($message && $message->getId()) {
            return $this->__('Edit AirMail Message Queue #%s', $this->escapeHtml($message->getId()));
        } else {
            return $this->__('New AirMail Message Queue');
        }
    }
}
