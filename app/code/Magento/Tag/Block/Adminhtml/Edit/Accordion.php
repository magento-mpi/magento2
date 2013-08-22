<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml tag accordion
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Tag_Block_Adminhtml_Edit_Accordion extends Magento_Adminhtml_Block_Widget_Accordion
{
    /**
     * Add products and customers accordion to layout
     *
     */
    protected function _prepareLayout()
    {
        if (is_null(Mage::registry('current_tag')->getId())) {
            return $this;
        }

        $tagModel = Mage::registry('current_tag');

        $this->setId('tag_customer_and_product_accordion');

        $this->addItem('tag_customer', array(
            'title'         => __('Customers Submitted this Tag'),
            'ajax'          => true,
            'content_url'   => $this->getUrl('*/*/customer', array('ret' => 'all', 'tag_id'=>$tagModel->getId(), 'store'=>$tagModel->getStoreId())),
        ));

        $this->addItem('tag_product', array(
            'title'         => __('Products Tagged by Customers'),
            'ajax'          => true,
            'content_url'   => $this->getUrl('*/*/product', array('ret' => 'all', 'tag_id'=>$tagModel->getId(), 'store'=>$tagModel->getStoreId())),
        ));
        return parent::_prepareLayout();
    }
}
