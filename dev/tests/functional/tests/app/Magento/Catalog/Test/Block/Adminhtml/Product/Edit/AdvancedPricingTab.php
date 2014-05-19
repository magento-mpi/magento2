<?php
/**
 * {license_notice}
 *
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
        $root = $element;
        $this->_rootElement->waitUntil(
            function () use ($root) {
                return $root->find('[data-tab-panel=advanced-pricing]')->isVisible();
            }
        );
        if (isset($fields['special_price']['value'])) {
            $container = $root->find('#attribute-special_price-container');
            Factory::getBlockFactory()
                ->getMagentoCatalogAdminhtmlProductEditAdvancedPricingTabSpecialOption($container)
                ->fill($fields['special_price']);
        }

        if (isset($fields['group_price']['value'])) {
            $button = $root->find('[title="Add Group Price"]');
            $container = $root->find('#attribute-group_price-container');
            foreach ($fields['group_price']['value'] as $rowId => $data) {
                $rowPrefix = 'group_price_row_' . $rowId;
                $button->click();
                $row = $container->find('//tr[td[select[@id="' . $rowPrefix . '_website"]]]', Locator::SELECTOR_XPATH);
                Factory::getBlockFactory()
                    ->getMagentoCatalogAdminhtmlProductEditAdvancedPricingTabGroupOption($row)
                    ->fill($rowPrefix, $data);
            }
        }
        if (isset($fields['tier_price']['value'])) {
            $button = $root->find('[title="Add Tier"]');

            $container = $root->find('#attribute-tier_price-container');
            foreach ($fields['tier_price']['value'] as $rowId => $data) {
                $rowPrefix = 'tier_price_row_' . $rowId;
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
