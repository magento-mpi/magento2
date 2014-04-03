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
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Factory\Factory;

/**
 * Custom Options Tab
 *
 * @package Magento\Catalog\Test\Block\Product
 */
class AdvancedPricingTab extends Tab
{
    /**
     * Fill group price options
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element)
    {
        if (!isset($fields['group_price']) && !isset($fields['tier_price'])) {
            return $this;
        }

        $root = $element;
        $this->_rootElement->waitUntil(
            function () use ($root) {
                return $root->find('#product_info_tabs_advanced-pricing_content')->isVisible();
            }
        );
        if (isset($fields['group_price'])) {
            $button = $root->find('[title="Add Group Price"]');

            $container = $root->find('#attribute-group_price-container');
            foreach ($fields['group_price']['value'] as $rowPrefix => $data) {
                $button->click();
                $row = $container->find('//tr[td[select[@id="' . $rowPrefix . '_website"]]]', Locator::SELECTOR_XPATH);
                Factory::getBlockFactory()
                    ->getMagentoCatalogAdminhtmlProductEditAdvancedPricingTabGroupOption($row)
                    ->fill($rowPrefix, $data);
            }
        }
        if (isset($fields['tier_price'])) {
            $button = $root->find('[title="Add Tier"]');

            $container = $root->find('#attribute-tier_price-container');
            foreach ($fields['tier_price']['value'] as $rowPrefix => $data) {
                $button->click();
                $row = $container->find('//tr[td[select[@id="' . $rowPrefix . '_website"]]]', Locator::SELECTOR_XPATH);
                Factory::getBlockFactory()
                    ->getMagentoCatalogAdminhtmlProductEditAdvancedPricingTabGroupOption($row)
                    ->fill($rowPrefix, $data);
            }
        }
        return $this;
    }
}
