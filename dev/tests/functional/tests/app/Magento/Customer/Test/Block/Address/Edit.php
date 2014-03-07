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

namespace Magento\Customer\Test\Block\Address;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Customer\Test\Fixture\Address;

/**
 * Class Edit
 * Customer address edit block
 *
 * @package Magento\Customer\Test\Block\Address
 */
class Edit extends Form
{
    /**
     * 'Save address' button
     *
     * @var string
     */
    protected $saveAddress = '.action.submit';

    /**
     * VAT field selector
     *
     * @var string
     */
    protected $vatFieldId = 'vat_id';

    /**
     * Edit customer address
     *
     * @param Address $fixture
     */
    public function editCustomerAddress(Address $fixture)
    {
        $this->fill($fixture);
        $this->_rootElement->find($this->saveAddress, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Save new VAT id
     *
     * @param $vat
     */
    public function saveVatID($vat)
    {
        $this->_rootElement->find($this->vatFieldId, Locator::SELECTOR_ID)->setValue($vat);
        $this->_rootElement->find($this->saveAddress, Locator::SELECTOR_CSS)->click();
    }
}
