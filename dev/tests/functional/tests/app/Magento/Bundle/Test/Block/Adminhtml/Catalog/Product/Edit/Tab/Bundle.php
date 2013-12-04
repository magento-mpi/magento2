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
use Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option;

/**
 * Class Bundle
 * Bundle options section
 *
 * @package Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab
 */
class Bundle extends Tab
{
    /**
     * Tab where bundle options section is placed
     */
    const GROUP_PRODUCT_DETAILS = 'product_info_tabs_product-details';

    /**
     * Bundle options block
     *
     * @var Option
     */
    private $bundleOptionBlock;

    /**
     * 'Create New Option' button
     *
     * @var Element
     */
    private $addNewOption;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->addNewOption = '#add_new_option';
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
     * Get bundle options block
     *
     * @param Element $context
     * @return Option
     */
    private function getBundleOptionBlock(Element $context = null)
    {
        $element = $context ? : $this->_rootElement;
        $this->bundleOptionBlock = Factory::getBlockFactory()
            ->getMagentoBundleAdminhtmlCatalogProductEditTabBundleOption(
                $element->find('#product_bundle_container')
            );

        return $this->bundleOptionBlock;
    }

    /**
     * Fill bundle options
     *
     * @param array $fields
     * @param Element $element
     */
    public function fillFormTab(array $fields, Element $element)
    {
        $blocksNumber = 0;
        foreach ($fields['bundle_selections']['value'] as $bundleOption) {
            $element->find($this->addNewOption, Locator::SELECTOR_CSS)->click();
            $bundleOptionsBlock = $this->getBundleOptionBlock($element);
            $bundleOptionsBlock->setBlockNumber($blocksNumber);
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
        $blocksNumber = 0;
        foreach ($fields['bundle_selections']['value'] as $bundleOption) {
            $bundleOptionsBlock = $this->getBundleOptionBlock($element);
            $bundleOptionsBlock->setBlockNumber($blocksNumber);
            $bundleOptionsBlock->expand();
            $bundleOptionsBlock->updateBundleOption($bundleOption, $element);
            $blocksNumber++;
        }
    }
}
