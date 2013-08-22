<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Additional Renderer of Product's Attribute Enable RMA control structure
 *
 * @category   Enterprise
 * @package    Enterprise_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Adminhtml_Product_Bundle_Items extends Magento_Core_Block_Template
{

    public function _construct()
    {
        parent::_construct();

        $this->setItems(Mage::registry('current_rma_bundle_item'));
        $this->setParentId((int)$this->getRequest()->getParam('item_id'));
    }
}
