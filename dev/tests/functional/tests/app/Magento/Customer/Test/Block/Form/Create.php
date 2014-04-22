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

namespace Magento\Customer\Test\Block\Form;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

/**
 * Customer create form for frontend
 *
 * @package Magento\Customer\Test\Block\Form
 */
class Create extends Form
{
    /**
     * Submit button
     *
     * @var string
     */
    protected $submitButton = '.action.submit';

    /**
     * Fill form with customer data and submit
     *
     * @param FixtureInterface $fixture
     */
    public function create(FixtureInterface $fixture)
    {
        $this->fill($fixture);
        $this->_rootElement->find($this->submitButton, Locator::SELECTOR_CSS)->click();
    }
}
