<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Paypal\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;
use Mtf\ObjectManager;

/**
 * Class Review
 * Paypal sandbox review block
 */
class ReviewExpress extends Block
{
    /**
     * Continue button
     *
     * @var string
     */
    protected $continue = '#confirmButtonTop';

    /**
     * Paypal review block
     *
     * @var string
     */
    protected $reviewExpressBlock = '#memberReview';

    /**
     * Paypal review block
     *
     * @var string
     */
    protected $oldReviewBlock = '//*[*[@id="memberReview"] or *[@id="reviewModule"]]';

    /**
     * Paypal review block
     *
     * @var string
     */
    protected $oldReviewExpressBlock = '#reviewModule';

    /**
     * Press 'Continue' button
     *
     * @return void
     */
    public function continueCheckout()
    {
        // Wait for page to load in order to check logged customer
        $element = $this->_rootElement;
        $selector = $this->oldReviewBlock;
        $element->waitUntil(
            function () use ($element, $selector) {
                return $element->find($selector, Locator::SELECTOR_XPATH)->isVisible() ? true : null;
            }
        );
        // PayPal returns different login pages due to buyer country
        if (!$this->_rootElement->find($this->reviewExpressBlock)->isVisible()) {
            $payPalReview = ObjectManager::getInstance()->create(
                '\Magento\Paypal\Test\Block\Review',
                [
                    'element' => $this->browser->find($this->oldReviewExpressBlock)
                ]
            );
            $payPalReview->continueCheckout();
            return;
        }
        $this->_rootElement->find($this->continue)->click();
    }
}
