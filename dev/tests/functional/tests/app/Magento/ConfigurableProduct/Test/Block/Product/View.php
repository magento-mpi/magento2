<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Product;

use \Magento\Catalog\Test\Block\Product\View as AbstractView;
use Mtf\Fixture\FixtureInterface;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;

class View extends AbstractView
{
    /**
     * @return \Magento\Catalog\Test\Block\Product\View\CustomOptions
     */
    protected function getOptionsBlock()
    {
        return $this->blockFactory->create(
            'Magento\Catalog\Test\Block\Product\View\CustomOptions',
            ['element' => $this->_rootElement->find('.product-options-wrapper')]
        );
    }

    /**
     * Fill in the option specified for the product
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function fillOptions(FixtureInterface $product)
    {
        /** @var ConfigurableProductInjectable $product */
        $attributesData = $product->getConfigurableAttributesData()['attributes_data'];
        $checkoutData = $product->getCheckoutData();
        $this->getOptionsBlock()->fillOptions($attributesData, $checkoutData);
    }
}
