<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesRule\Block\Adminhtml\Promo\Quote\Edit;

/**
 * description
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('promo_catalog_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Shopping Cart Price Rule'));
    }
}
