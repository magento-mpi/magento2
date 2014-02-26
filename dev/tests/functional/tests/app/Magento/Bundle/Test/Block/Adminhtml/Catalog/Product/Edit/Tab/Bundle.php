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

namespace Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class Bundle
 * Bundle options section
 */
class Bundle extends Tab
{
    /**
     * Tab where bundle options section is placed
     */
    const GROUP_PRODUCT_DETAILS = 'product_info_tabs_product-details';

    /**
     * 'Create New Option' button
     *
     * @var string
     */
    protected $addNewOption = '#add_new_option';

    /**
     * Bundle options block
     *
     * @var string
     */
    protected $bundleOptionBlock = '#bundle_option_';

    /**
     * Get bundle options block
     *
     * @param int $blockNumber
     * @param Element $context
     * @return \Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option
     */
    protected function getBundleOptionBlock($blockNumber, Element $context = null)
    {
        $element = $context ? : $this->_rootElement;
        return Factory::getBlockFactory()->getMagentoBundleAdminhtmlCatalogProductEditTabBundleOption(
            $element->find($this->bundleOptionBlock . $blockNumber)
        );
    }

    /**
     * Open bundle options section
     *
     * @param Element $context
     */
    public function open(Element $context = null)
    {
        $element = $context ? : $this->_rootElement;
        $element->find(static::GROUP_PRODUCT_DETAILS, Locator::SELECTOR_ID)->click();
    }

    /**
     * Fill bundle options
     *
     * @param array $fields
     * @param Element $element
     */
    public function fillFormTab(array $fields, Element $element)
    {
        $bundleOptions = $this->prepareBundleOptions($fields['bundle_selections']['value']);
        $blocksNumber = 0;
        foreach ($bundleOptions as $bundleOption) {
            $element->find($this->addNewOption)->click();
            $bundleOptionsBlock = $this->getBundleOptionBlock($blocksNumber, $element);
            $bundleOptionsBlock->fillBundleOption($bundleOption, $element);
            $blocksNumber++;
        }
    }

    /**
     * Update bundle options
     *
     * @param array $fields
     * @param Element $element
     */
    public function updateFormTab(array $fields, Element $element)
    {
        $bundleOptions = $this->prepareBundleOptions($fields['bundle_selections']['value']);
        $blocksNumber = 0;
        foreach ($$bundleOptions as $bundleOption) {
            $bundleOptionsBlock = $this->getBundleOptionBlock($blocksNumber, $element);
            $bundleOptionsBlock->expand();
            $bundleOptionsBlock->updateBundleOption($bundleOption, $element);
            $blocksNumber++;
        }
    }

    /**
     * Prepare Bundle Options array from preset
     * @param array $bundleSelections
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function prepareBundleOptions(array $bundleSelections)
    {
        if (!isset($bundleSelections['preset'])) {
            return $bundleSelections;
        }

        $preset = $bundleSelections['preset'];
        $products = $bundleSelections['products'];
        foreach ($preset['items'] as & $item) {
            foreach ($item['assigned_products'] as $productIncrement => & $selection) {
                if (!isset($products[$productIncrement])) {
                    throw new \InvalidArgumentException(
                        sprintf('Not sufficient number of products for bundle preset: %s', $preset['name'])
                    );
                }
                /** @var $fixture CatalogProductSimple */
                $fixture = $products[$productIncrement];
                $selection['search_data']['name'] = $fixture->getName();
                $selection['data']['product_id']['value'] = $fixture->getId();
            }
        }
        return $preset['items'];
    }
}
