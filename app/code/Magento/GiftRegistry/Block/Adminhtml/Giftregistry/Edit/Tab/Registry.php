<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tab_Registry
    extends Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_Attribute
{
    protected function _construct()
    {
        parent::_construct();
        $this->setFormTitle(__('Attributes'));
    }

    /**
     * Get field prefix
     *
     * @return string
     */
    public function getFieldPrefix()
    {
        return 'registry';
    }
}
