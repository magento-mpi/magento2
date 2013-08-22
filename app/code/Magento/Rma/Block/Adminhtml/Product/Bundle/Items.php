<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Additional Renderer of Product's Attribute Enable RMA control structure
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rma_Block_Adminhtml_Product_Bundle_Items extends Magento_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();

        $this->setItems(Mage::registry('current_rma_bundle_item'));
        $this->setParentId((int)$this->getRequest()->getParam('item_id'));
    }
}
