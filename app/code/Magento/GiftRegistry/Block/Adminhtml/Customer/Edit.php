<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Adminhtml_Customer_Edit
    extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Intialize form
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_GiftRegistry';
        $this->_controller = 'adminhtml_customer';

        parent::_construct();

        $this->_removeButton('reset');
        $this->_removeButton('save');

        $confirmMessage = __('Are you sure you want to delete this gift registry?');
        $this->_updateButton('delete', 'label', __('Delete Registry'));
        $this->_updateButton('delete', 'onclick',
                'deleteConfirm(\'' . $this->jsQuoteEscape($confirmMessage) . '\', \'' . $this->getDeleteUrl() . '\')'
            );
    }

    /**
     * Return form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $entity = Mage::registry('current_giftregistry_entity');
        if ($entity->getId()) {
            return $this->escapeHtml($entity->getTitle());
        }
        return __('Gift Registry Entity');
    }

    /**
     * Retrieve form back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        $customerId = null;
        if (Mage::registry('current_giftregistry_entity')) {
            $customerId = Mage::registry('current_giftregistry_entity')->getCustomerId();
        }
        return $this->getUrl('*/customer/edit', array('id' => $customerId, 'active_tab' => 'giftregistry'));
    }
}
