<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create;

use Magento\Sales\Test\Fixture\Order;
use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Class Customer
 * Customer selection grid
 *
 */
class Customer extends Grid
{
    /**
     * 'Create New Customer' button
     *
     * @var string
     */
    protected $createNewCustomer = '.actions button';

    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = 'td[data-column=email]';

    /**
     * {@inheritdoc}
     */
    protected $filters = array(
        'email' => array(
            'selector' => '#sales_order_create_customer_grid_filter_email'
        )
    );

    /**
     * Select customer if it is present in fixture or click create new customer button
     *
     * @param Order $fixture
     */
    public function selectCustomer(Order $fixture)
    {
        $customer = $fixture->getCustomer();
        if (empty($customer)) {
            $this->_rootElement->find($this->createNewCustomer)->click();
        } else {
            $this->searchAndOpen(array(
                'email' => $customer->getEmail()
            ));
        }
        $this->getTemplateBlock()->waitLoader();
    }
}
