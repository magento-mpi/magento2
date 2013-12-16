<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * description
 *
 * @category    Magento
 * @category   Magento
 * @package    Magento_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogRule\Block\Adminhtml\Promo\Catalog\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('promo_catalog_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Catalog Price Rule'));
    }
}
