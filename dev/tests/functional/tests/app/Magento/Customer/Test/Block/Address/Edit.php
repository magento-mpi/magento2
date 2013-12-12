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
     * 'region' input element when Country selected does not have a pre-defined pull-down list of regions
     *
     * @var string
     */
    protected $inputRegion = '#region';

    /**
     * {@inheritdoc}
     */
    protected $_mapping = array(
        'firstname' => '#firstname',
        'lastname' => '#lastname',
        'company' => '#company',
        'telephone' => '#telephone',
        'street_1' => '#street_1',
        'city' => '#city',
        'region' => '#region_id',
        'postcode' => '#zip',
        'country' => '#country',
    );

    /**
     * Fill form data. Unset 'email' field as it absent in current form.  Region field requires special handling.
     *
     * @param array $fields
     * @param Element $element
     */
    protected function _fill(array $fields, Element $element = null)
    {
        unset($fields['email']);

        $regionValue = $fields['region']['value'];
        unset($fields['region']);

        parent::_fill($fields);

        // Region is either a SELECT or an INPUT depending on the selected Country
        $regionElement = $this->_rootElement->find($this->_mapping['region'], Locator::SELECTOR_CSS, 'select');
        if (!$regionElement->isVisible()) {
            // region_id select is not visible, swap in the region input field
            $regionElement = $this->_rootElement->find($this->inputRegion);
        }
        $regionElement->setValue($regionValue);
    }

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
}
