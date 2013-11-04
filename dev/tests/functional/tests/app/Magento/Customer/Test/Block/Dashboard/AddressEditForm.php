<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Dashboard;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;
use Magento\Customer\Test\Fixture\Address;

/**
 * Customer Address Edit form
 *
 * @package Magento\Customer\Test\Block\Dashboard
 */
class AddressEditForm extends Form
{
    /**
     * Submit button
     *
     * @var string
     */
    private $submitButton;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Initialize mapping
        $this->_mapping = array(
            'firstname' => '#firstname',
            'lastname' => '#lastname',
            'telephone' => '#telephone',
            'street_1' => '#street_1',
            'city' => '#city',
            'region' => '#region_id',
            'postcode' => '#zip',
            'country' => '#country',
        );
        //Elements
        $this->submitButton = '.action.submit';
    }

    /**
     * Fill address and submit form
     *
     * @param Address $fixture
     */
    public function saveAddress(Address $fixture)
    {
        $this->fill($fixture);
        $this->submit();
    }

    /**
     * Submit form
     */
    public function submit()
    {
        $this->_rootElement->find($this->submitButton, Locator::SELECTOR_CSS)->click();
    }
}
