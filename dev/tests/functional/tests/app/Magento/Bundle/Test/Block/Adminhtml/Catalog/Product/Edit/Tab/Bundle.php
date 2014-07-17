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
     * Open option section
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
            ['element' => $this->_rootElement->find('#bundle_option_' . $blockNumber)]
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
        foreach ($fields['bundle_selections']['value']['bundle_options'] as $key => $bundleOption) {
            $itemOption = $this->_rootElement->find(sprintf($this->openOption, $key));
            if ($itemOption->isVisible()) {
                $itemOption->click();
            } else {
                $this->_rootElement->find($this->addNewOption)->click();
            }
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
        foreach ($fields['bundle_selections']['value']['bundle_options'] as $key => &$bundleOption) {
            $this->_rootElement->find(sprintf($this->openOption, $index))->click();
            foreach ($bundleOption['assigned_products'] as &$product) {
                $product['data']['getProductName'] = $product['search_data']['name'];
            }
            $newFields['bundle_selections'][$key] = $this->getBundleOptionBlock($key)
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
        $blocksNumber = 0;
        foreach ($fields['bundle_selections']['value']['bundle_options'] as $bundleOption) {
            $bundleOptionsBlock = $this->getBundleOptionBlock($blocksNumber++, $element);
            $bundleOptionsBlock->expand();
            $bundleOptionsBlock->updateBundleOption($bundleOption, $element);
        }
    }
}
