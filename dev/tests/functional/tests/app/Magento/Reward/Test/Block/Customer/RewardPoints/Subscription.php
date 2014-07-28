<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Block\Customer\RewardPoints;

use Mtf\Block\Form;

/**
 * Class Subscription
 * Form for reward points subscription
 */
class Subscription extends Form
{
    /**
     * Selector for 'Save Subscription Settings' button
     *
     * @var string
     */
    protected $saveButtonSelector = '.save';

    /**
     * Click on 'Save Subscription Settings' button
     *
     * @return void
     */
    public function clickSaveButton()
    {
        $this->_rootElement->find($this->saveButtonSelector)->click();
    }
}
