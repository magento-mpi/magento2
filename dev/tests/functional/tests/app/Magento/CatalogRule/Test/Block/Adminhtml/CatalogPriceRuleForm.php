<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Block\Adminhtml;

use Mtf\Fixture;
use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class CatalogPriceRuleForm
 * Form for creation of a Catalog Price Rule
 *
 * @package Magento\CatalogRule\Test\Block\Backend
 */
class CatalogPriceRuleForm extends FormTabs
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        // Custom tab classes for catalog price rule form
        $this->_tabClasses = array(
            'promo_catalog_edit_tabs_conditions_section' =>
                '\\Magento\\CatalogRule\\Test\\Block\\Adminhtml\\Promo\\Catalog\\Edit\\Tab\\Conditions',
        );

        // Save button
        $this->saveButton = '#save';
    }
}