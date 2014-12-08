<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\Block;

use Magento\AdvancedCheckout\Test\Block\Sku\Products\Info;
use Magento\Checkout\Test\Block\Cart;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AdvancedCheckout
 * AdvancedCheckout cart block
 */
class AdvancedCheckoutCart extends Cart
{
    /**
     * Failed item block selector
     *
     * @var string
     */
    protected $failedItem = '//*[@class="cart item" and .//tr[contains(@class,"info") and .//div[contains(.,"%s")]]]';

    /**
     * Get failed item block
     *
     * @param FixtureInterface|string $product
     * @return Info
     */
    protected function getFailedItemBlock($product)
    {
        $failedItemBlockSelector = $product instanceof FixtureInterface
            ? sprintf($this->failedItem, $product->getSku())
            : sprintf($this->failedItem, 'nonExistentSku');

        return $this->blockFactory->create(
            'Magento\AdvancedCheckout\Test\Block\Sku\Products\Info',
            ['element' => $this->_rootElement->find($failedItemBlockSelector, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Get error message in failed item block
     *
     * @param FixtureInterface $product
     * @return string
     */
    public function getFailedItemErrorMessage(FixtureInterface $product)
    {
        $failedItemBlock = $this->getFailedItemBlock($product);

        return $failedItemBlock->getErrorMessage();
    }

    /**
     * Check that "Specify the product's options" link is visible
     *
     * @param FixtureInterface $product
     * @return bool
     */
    public function specifyProductOptionsLinkIsVisible(FixtureInterface $product)
    {
        $failedItemBlock = $this->getFailedItemBlock($product);

        return $failedItemBlock->linkIsVisible();
    }

    /**
     * Click "Specify the product's options" link
     *
     * @param FixtureInterface $product
     * @return void
     */
    public function clickSpecifyProductOptionsLink(FixtureInterface $product)
    {
        $failedItemBlock = $this->getFailedItemBlock($product);
        $failedItemBlock->clickOptionsLink();
    }

    /**
     * Get tier price messages in failed item block
     *
     * @param FixtureInterface $product
     * @return array
     */
    public function getTierPriceMessages(FixtureInterface $product)
    {
        $failedItemBlock = $this->getFailedItemBlock($product);

        return $failedItemBlock->getTierPriceMessages();
    }

    /**
     * Get failed item error message
     *
     * @param FixtureInterface $product
     * @return bool
     */
    public function isMsrpNoticeDisplayed(FixtureInterface $product)
    {
        $failedItemBlock = $this->getFailedItemBlock($product);

        return $failedItemBlock->isMsrpNoticeDisplayed();
    }

    /**
     * Delete product
     *
     * @param FixtureInterface|string $product
     * @return void
     */
    public function deleteProduct($product)
    {
        $failedItemBlock = $this->getFailedItemBlock($product);
        $failedItemBlock->deleteProduct();
    }

    /**
     * Check that failed product block visible
     *
     * @param FixtureInterface $product
     * @return bool
     */
    public function isFailedItemBlockVisible(FixtureInterface $product)
    {
        return $this->getFailedItemBlock($product)->isVisible();
    }
}
