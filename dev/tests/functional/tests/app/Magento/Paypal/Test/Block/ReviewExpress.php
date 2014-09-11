<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
     * Press 'Continue' button
     *
     * @return void
     */
    public function continueCheckout()
    {
        // Wait for page to load in order to check logged customer
        $this->_rootElement->find($this->oldReviewBlock, Locator::SELECTOR_XPATH)->click();
        // PayPal returns different login pages due to buyer country
        if (!$this->_rootElement->find($this->reviewExpressBlock)->isVisible()) {
            $payPalReview = ObjectManager::getInstance()->create(
                '\Magento\Paypal\Test\Block\Review',
                [
                    'element' => $this->browser->find($this->oldReviewBlock)
                ]
            );
            $payPalReview->continueCheckout();
            return;
        }
        $this->_rootElement->find($this->continue)->click();
    }
}
