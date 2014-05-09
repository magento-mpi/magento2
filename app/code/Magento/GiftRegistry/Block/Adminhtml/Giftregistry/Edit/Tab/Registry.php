<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Tab;

class Registry extends \Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Attribute\Attribute
{
    /**
     * @return void
     */
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
