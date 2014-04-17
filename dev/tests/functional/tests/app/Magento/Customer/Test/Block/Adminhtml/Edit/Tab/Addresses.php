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

namespace Magento\Customer\Test\Block\Adminhtml\Edit\Tab;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Custom Addresses tab
 *
 * @package Magento\Customer\Test\Block\Adminhtml\Edit\Tab
 */
class Addresses extends Tab
{
    /**
     * @var string
     */
    protected $addNewAddress = '#add_address_button';

    /**
     * @var string
     */
    protected $selectedAddress = './/*[@id="address_list"]/li[@aria-selected="true"]';

    /**
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element)
    {
        if (! $this->_rootElement->find($this->selectedAddress, Locator::SELECTOR_XPATH)->isVisible()) {
            $this->addNewAddress();
        }
        parent::fillFormTab($fields, $element);

        return $this;
    }

    /**
     * @return $this
     */
    public function addNewAddress()
    {
        $this->_rootElement->find($this->addNewAddress)->click();
        return $this;
    }

    /**
     * @param int $number
     * @return $this
     */
    public function selectAddress($number)
    {
        $this->_rootElement->find('//*[@id="address_list"]/li[' . $number . ']/a', Locator::SELECTOR_XPATH)->click();
        return $this;
    }
}
