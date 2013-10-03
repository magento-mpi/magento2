<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Tab;

class Registry
    extends \Magento\GiftRegistry\Block\Adminhtml\Giftregistry\Edit\Attribute\Attribute
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
