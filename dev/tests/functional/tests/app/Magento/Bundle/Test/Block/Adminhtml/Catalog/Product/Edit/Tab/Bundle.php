<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Fixture\InjectableFixture;
use Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option;

/**
 * Class Bundle
 * Bundle options section block on product-details tab
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
     * 'Open Option' button
     *
     * @var string
     */
    protected $openOption = '[data-target="#bundle_option_%d-content"]';

    /**
     * Get bundle options block
     *
     * @param int $blockNumber
     * @return Option
     */
    protected function getBundleOptionBlock($blockNumber)
    {
        return $this->blockFactory->create(
            'Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option',
            ['element' => $this->_rootElement->find('#' . $blockNumber)]
        );
    }

    /**
     * Fill bundle options
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        if (!isset($fields['bundle_selections'])) {
            return $this;
        }
        $bundleOptions = $this->prepareBundleOptions($fields['bundle_selections']['value']);
        foreach ($bundleOptions as $key => $bundleOption) {
            $this->_rootElement->find($this->addNewOption)->click();
            $this->getBundleOptionBlock($key)->fillBundleOption($bundleOption);
        }
        return $this;
    }

    /**
     * Get data to fields on downloadable tab
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $newFields = [];
        if (!isset($fields['bundle_selections'])) {
            return $this;
        }
        $index = 0;
        $bundleOptions = $this->prepareBundleOptions($fields['bundle_selections']['value'], true);
        foreach ($bundleOptions as $key => $bundleOption) {
            $this->_rootElement->find(sprintf($this->openOption, $index))->click();
            $newFields['bundle_selections']['bundle_option_' . $key] = $this->getBundleOptionBlock($key)
                ->getBundleOptionData($bundleOption);
            $index++;
        }

        return $newFields;
    }

    /**
     * Update bundle options
     *
     * @param array $fields
     * @param Element|null $element
     * @return void
     */
    public function updateFormTab(array $fields, Element $element = null)
    {
        if (!isset($fields['bundle_selections'])) {
            return;
        }
        $bundleOptions = $this->prepareBundleOptions($fields['bundle_selections']['value']);
        $blocksNumber = 0;
        foreach ($$bundleOptions as $bundleOption) {
            $bundleOptionsBlock = $this->getBundleOptionBlock($blocksNumber++, $element);
            $bundleOptionsBlock->expand();
            $bundleOptionsBlock->updateBundleOption($bundleOption, $element);
        }
    }

    /**
     * Prepare Bundle Options array from preset
     *
     * @param array $bundleSelections
     * @param bool $priceType
     * @return array|null
     */
    protected function prepareBundleOptions(array $bundleSelections, $priceType = false)
    {
        if (!isset($bundleSelections['preset'])) {
            return $bundleSelections;
        }
        $preset = $bundleSelections['preset'];
        $products = $bundleSelections['products'];
        foreach ($preset as & $item) {
            foreach ($item['assigned_products'] as $productIncrement => & $selection) {
                if (!isset($products[$productIncrement])) {
                    break;
                }
                /** @var InjectableFixture $fixture */
                $fixture = $products[$productIncrement];
                if ($priceType !== false) {
                    $newData = [];
                    $newData['getProductName'] = $fixture->getData('name');
                    $newData['selection_qty'] = $selection['data']['selection_qty'];
                    if (isset ($selection['data']['selection_price_value'])) {
                        $newData['selection_price_value'] = $selection['data']['selection_price_value'];
                    }
                    if (isset ($selection['data']['selection_price_type'])) {
                        $newData['selection_price_type'] = $selection['data']['selection_price_type'];
                    }
                    $item['assigned_products'][$productIncrement] = $newData;
                } else {
                    $selection['search_data']['name'] = $fixture->getData('name');
                }
            }
        }
        return $preset;
    }
}
