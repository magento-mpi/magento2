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

namespace Magento\CatalogRule\Test\Block\Adminhtml\Promo\Catalog;

use Mtf\Fixture\FixtureInterface;
use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class Edit
 * Form for creation of a Catalog Price Rule
 *
 * @package Magento\CatalogRule\Test\Block\Backend
 */
class Edit extends FormTabs
{
    /**
     * {@inheritdoc}
     */
    protected $tabClasses = array(
        'promo_catalog_edit_tabs_conditions_section' =>
        '\\Magento\\CatalogRule\\Test\\Block\\Adminhtml\\Promo\\Catalog\\Edit\\Tab\\Conditions',
    );
}