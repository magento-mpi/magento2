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
use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class Bundle
 * Bundle options section
 */
class Bundle extends Tab
{
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
     * @return \Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option
     */
    protected function getBundleOptionBlock($blockNumber)
    {
        return Factory::getBlockFactory()->getMagentoBundleAdminhtmlCatalogProductEditTabBundleOption(
            $this->_rootElement->find($this->bundleOptionBlock . $blockNumber)
        );
    }

    /**
     * Fill bundle options
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element)
    {
        if (!isset($fields['bundle_selections'])) {
            return $this;
        }
        $bundleOptions = $this->prepareBundleOptions($fields['bundle_selections']['value']);
        $blocksNumber = 0;
        foreach ($bundleOptions as $bundleOption) {
            $this->_rootElement->find($this->addNewOption)->click();
            $bundleOptionsBlock = $this->getBundleOptionBlock($blocksNumber);
            $bundleOptionsBlock->fillBundleOption($bundleOption, $this->_rootElement);
            $blocksNumber++;
        }

        return $this;
    }

    /**
     * Update bundle options
     *
     * @param array $fields
     * @param Element $element
     * @return void
     */
    public function updateFormTab(array $fields, Element $element)
    {
        if (!isset($fields['bundle_selections'])) {
            return;
        }
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
     *
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
