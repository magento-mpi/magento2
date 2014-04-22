<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Block\Adminhtml\Edit\Tab;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Addresses
 * Customer addresses edit block
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
     * Check exist selected tab for address
     *
     * @var string
     */
    protected $selectedAddress = './/*[@id="address_list"]/li[@aria-selected="true"]';

    /**
     * Open inner tab of address
     *
     * @var string
     */
    protected $addressTab = '//*[@id="address_list"]/li[%d]/a';

    /**
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element)
    {
        if (!$this->_rootElement->find($this->selectedAddress, Locator::SELECTOR_XPATH)->isVisible()) {
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
}
