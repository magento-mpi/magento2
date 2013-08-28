<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Tab_Registry
    extends Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_Attribute
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
