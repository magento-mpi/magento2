<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\AdvancedCheckout\Test\Block;

use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;
use Magento\Checkout\Test\Block\Cart;
use Magento\AdvancedCheckout\Test\Block\Sku\Products\Info;

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
    protected $failedItem = '//*[@id="failed-products-table"]//tr[contains(@class,"info") and //div[contains(.,"%s")]]';

    /**
     * Get failed item block
     *
     * @param FixtureInterface $product
     * @return Info
     */
    protected function getFailedItemBlock(FixtureInterface $product)
    {
        $failedItemBlockSelector = sprintf($this->failedItem, $product->getSku());

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
}
