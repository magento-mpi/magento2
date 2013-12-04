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

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Factory\Factory;

/**
 * Custom Options Tab
 *
 * @package Magento\Catalog\Test\Block\Product
 */
class CustomOptionsTab extends Tab
{
    /**
     * Fill variations fieldset
     *
     * @param array $fields
     * @param Element $element
     */
    public function fillFormTab(array $fields, Element $element)
    {
        $root = $element;
        $this->_rootElement->waitUntil(
            function () use ($root) {
                return $root->find('#Custom_Options')->isVisible();
            }
        );

        $button = $root->find('[data-ui-id="admin-product-options-add-button"]');

        $container = $root->find('#product_options_container');

        foreach ($fields['custom_options']['value'] as $index => $data) {
            $button->click();
            $row = $container->find('.fieldset-wrapper:nth-child(' . ($index + 1) . ')');
            Factory::getBlockFactory()
                ->getMagentoCatalogAdminhtmlProductEditCustomOptionsTabOption($row)
                ->fill($data);
        }
    }
}
