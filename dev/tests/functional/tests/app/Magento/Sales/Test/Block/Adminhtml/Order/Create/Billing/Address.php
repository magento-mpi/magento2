<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create\Billing;

use \Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class BillingAddress
 * Adminhtml sales order billing address block
 *
 */
class Address extends Form
{
    /**
     * Backend abstract block
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Selector for existing customer addresses dropdown
     *
     * @var string
     */
    protected $existingAddressSelector = '#order-billing_address_customer_address_id';

    /**
     * Get existing customer addresses
     *
     * @return array
     */
    public function getExistingAddresses()
    {
        $this->getTemplateBlock()->waitLoader();
        $this->reinitRootElement();
        return explode("\n", $this->_rootElement->find($this->existingAddressSelector)->getText());
    }

    /**
     * Get backend abstract block
     *
     * @return \Magento\Backend\Test\Block\Template
     */
    protected function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Magento\Backend\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
