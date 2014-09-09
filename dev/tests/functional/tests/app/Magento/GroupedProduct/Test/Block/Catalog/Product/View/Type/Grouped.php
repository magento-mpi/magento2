<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Block\Catalog\Product\View\Type;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;
use Magento\GroupedProduct\Test\Fixture\GroupedProductInjectable;

/**
 * Class Grouped
 * Grouped product blocks on frontend
 */
class Grouped extends Block
{
    /**
     * Selector for sun product block by name
     *
     * @var string
     */
    protected $subProductByName = './/tr[./td[contains(@class,"item")] and .//*[contains(.,"%s")]]';

    /**
     * Selector for qty of sub product
     *
     * @var string
     */
    protected $qty = '[name^="super_group"]';

    /**
     * Selector qty for sub product by id
     *
     * @var string
     */
    protected $qtySubProductById = '[name="super_group[%d]"]';

    /**
     * Get qty for subProduct
     *
     * @param int $subProductId
     * @return string
     */
    public function getQty($subProductId)
    {
        return $this->_rootElement->find(sprintf($this->qtySubProductById, $subProductId))->getValue();
    }

    /**
     * Fill links on product view page
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function fill(FixtureInterface $product)
    {
        /** @var GroupedProductInjectable $product */
        $associatedProducts = $product->getAssociated()['products'];
        $data = $product->getCheckoutData()['options'];

        // Replace link key to label
        foreach ($data  as $key => $productData) {
            $productKey = str_replace('product_key_', '', $productData['product_name']);
            $data[$key]['product_name'] = $associatedProducts[$productKey]->getName();
        }

        // Fill
        foreach ($data as $productData) {
            $subProduct = $this->_rootElement->find(
                sprintf($this->subProductByName, $productData['product_name']),
                Locator::SELECTOR_XPATH
            );
            $subProduct->find($this->qty)->setValue($productData['qty']);
        }
    }
}
