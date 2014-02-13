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
 * Catalog price rules
 *
 * @category    Magento
 * @category   Magento
 * @package    Magento_CatalogRule
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\CatalogRule\Block\Adminhtml\Promo;

class Catalog extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_addButton('apply_rules', array(
            'label'     => __('Apply Rules'),
            'onclick'   => "location.href='".$this->getUrl('catalog_rule/*/applyRules')."'",
            'class'     => 'apply',
        ));

        $this->_blockGroup = 'Magento_CatalogRule';
        $this->_controller = 'adminhtml_promo_catalog';
        $this->_headerText = __('Catalog Price Rules');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();

    }
}
