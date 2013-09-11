<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Block\Adminhtml\Customer\Edit;

class Form
    extends \Magento\Adminhtml\Block\Widget\Form
{

    protected $_template = 'customer/form.phtml';

    /**
     * Prepare layout
     *
     * @return \Magento\GiftRegistry\Block\Adminhtml\Customer\Edit\Form
     */
    protected function _prepareLayout()
    {
        $this->addChild('entity_items', '\Magento\GiftRegistry\Block\Adminhtml\Customer\Edit\Items');
        $this->addChild('cart_items', '\Magento\GiftRegistry\Block\Adminhtml\Customer\Edit\Cart');
        $this->addChild('sharing_form', '\Magento\GiftRegistry\Block\Adminhtml\Customer\Edit\Sharing');
        $this->addChild('update_button', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label' => __('Update Items and Qty\'s'),
            'type'  => 'submit'
        ));

        return parent::_prepareLayout();
    }

    /**
     * Retrieve website name
     *
     * @return string
     */
    public function getWebsiteName()
    {
        return \Mage::app()->getWebsite($this->getEntity()->getWebsiteId())->getName();
    }

    /**
     * Retrieve owner name
     *
     * @return string
     */
    public function getOwnerName()
    {
        $customer = \Mage::getModel('Magento\Customer\Model\Customer')
            ->load($this->getEntity()->getCustomerId());

        return $this->escapeHtml($customer->getName());
    }

    /**
     * Retrieve customer edit form url
     *
     * @return string
     */
    public function getOwnerUrl()
    {
        return $this->getUrl('*/customer/edit', array('id' => $this->getEntity()->getCustomerId()));
    }

    /**
     * Retrieve gift registry type name
     *
     * @return string
     */
    public function getTypeName()
    {
        $type = \Mage::getModel('Magento\GiftRegistry\Model\Type')
            ->load($this->getEntity()->getTypeId());

        return $this->escapeHtml($type->getLabel());
    }

   /**
     * Retrieve escaped entity title
     *
     * @return string
     */
    public function getEntityTitle()
    {
        return $this->escapeHtml($this->getEntity()->getTitle());
    }

   /**
     * Retrieve escaped entity message
     *
     * @return string
     */
    public function getEntityMessage()
    {
        return $this->escapeHtml($this->getEntity()->getMessage());
    }

   /**
     * Retrieve list of registrants
     *
     * @return string
     */
    public function getRegistrants()
    {
        return $this->escapeHtml($this->getEntity()->getRegistrants());
    }

   /**
     * Return gift registry entity object
     *
     * @return \Magento\GiftRegistry\Model\Entity
     */
    public function getEntity()
    {
        return \Mage::registry('current_giftregistry_entity');
    }

   /**
     * Return shipping address
     *
     * @return \Magento\GiftRegistry\Model\Entity
     */
    public function getShippingAddressHtml()
    {
        return $this->getEntity()->getFormatedShippingAddress();
    }

   /**
     * Return gift registry creation data
     *
     * @return \Magento\GiftRegistry\Model\Entity
     */
    public function getCreatedAt()
    {
        return $this->formatDate($this->getEntity()->getCreatedAt(),
            \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM, true
        );
    }

    /**
     * Return update items form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/update', array('_current' => true));
    }
}
