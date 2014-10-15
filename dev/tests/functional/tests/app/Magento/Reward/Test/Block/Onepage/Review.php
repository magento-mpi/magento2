<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Block\Onepage;

use Mtf\Client\Element\Locator;

/**
 * Class Review
 * One page checkout order review block
 */
class Review extends \Magento\Checkout\Test\Block\Onepage\Review
{
    /**
     * Remove reward points
     *
     * @var string
     */
    protected $removeButton = '.rewardpoints .action.delete';

    /**
     * Notification agreements locator
     *
     * @var string
     */
    protected $notification = 'div.mage-error';

    /**
     * Agreement locator
     *
     * @var string
     */
    protected $agreement = './/input[contains(@id, "agreement")]';

    /**
     * Click on 'Remove' reward points link
     *
     * @return void
     */
    public function clickRemoveRewardPoints()
    {
        $this->_rootElement->find($this->removeButton)->click();
    }

    /**
     * Get notification massage
     *
     * @return string
     */
    public function getNotificationMassage()
    {
        return $this->_rootElement->find($this->notification)->getText();
    }

    /**
     * Set agreement
     *
     * @param string $value
     * @return void
     */
    public function setAgreement($value)
    {
        $this->_rootElement->find($this->agreement, Locator::SELECTOR_XPATH, 'checkbox')->setValue($value);
    }

    /**
     * Check agreement
     *
     * @return bool
     */
    public function checkAgreement()
    {
        return $this->_rootElement->find($this->agreement, Locator::SELECTOR_XPATH)->isVisible();
    }
}
