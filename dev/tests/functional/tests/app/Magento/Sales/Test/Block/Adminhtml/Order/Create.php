<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order;

use Magento\Customer\Test\Fixture\AddressInjectable;
use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Sales\Test\Fixture\Order;

/**
 * Class Create
 * Adminhtml sales order create block
 *
 */
class Create extends Block
{
    /**
     * Sales order create items block
     *
     * @var string
     */
    protected $itemsBlock = '#order-items';

    /**
     * Sales order create search products block
     *
     * @var string
     */
    protected $gridBlock = '#order-search';

    /**
     * Sales order create billing address block
     *
     * @var string
     */
    protected $billingAddressBlock = '#order-billing_address';

    /**
     * Sales order create shipping address block
     *
     * @var string
     */
    protected $shippingAddressBlock = '#order-shipping_address';

    /**
     * Sales order create payment method block
     *
     * @var string
     */
    protected $billingMethodBlock = '#order-billing_method';

    /**
     * Sales order create shipping method block
     *
     * @var string
     */
    protected $shippingMethodBlock = '#order-shipping_method';

    /**
     * Sales order create totals block
     *
     * @var string
     */
    protected $totalsBlock = '#order-totals';

    /**
     * Backend abstract block
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Order items grid block
     *
     * @var string
     */
    protected $orderItemsGrid = '#order-items_grid';

    /**
     * Getter for order selected products grid
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\Items
     */
    public function getItemsBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderCreateItems(
            $this->_rootElement->find($this->itemsBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get sales order create billing address block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\Billing\Address
     */
    public function getBillingAddressBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderCreateBillingAddress(
            $this->_rootElement->find($this->billingAddressBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get sales order create billing address block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\Shipping\Address
     */
    protected function getShippingAddressBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderCreateShippingAddress(
            $this->_rootElement->find($this->shippingAddressBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get sales order create payment method block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\Billing\Method
     */
    protected function getBillingMethodBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderCreateBillingMethod(
            $this->_rootElement->find($this->billingMethodBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get sales order create shipping method block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\Shipping\Method
     */
    protected function getShippingMethodBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderCreateShippingMethod(
            $this->_rootElement->find($this->shippingMethodBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get sales order create totals block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\Totals
     */
    protected function getTotalsBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderCreateTotals(
            $this->_rootElement->find($this->totalsBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get backend abstract block
     *
     * @return \Magento\Backend\Test\Block\Template
     */
    protected function getTemplateBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendTemplate(
            $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)
        );
    }

    /**
     * Get sales order create search products block
     *
     * @return \Magento\Sales\Test\Block\Adminhtml\Order\Create\Search\Grid
     */
    protected function getGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoSalesAdminhtmlOrderCreateSearchGrid(
            $this->_rootElement->find($this->gridBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Wait display order items grid
     *
     * @return void
     */
    public function waitOrderItemsGrid()
    {
        $this->waitForElementVisible($this->orderItemsGrid);
    }

    /**
     * Add products to order
     *
     * @param Order $fixture
     * @return void
     */
    public function addProducts(Order $fixture)
    {
        $this->waitForElementVisible($this->itemsBlock);
        $this->getItemsBlock()->clickAddProducts();
        $this->getGridBlock()->selectProducts($fixture);
        //Loader appears twice
        $this->getTemplateBlock()->waitLoader();
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Fill addresses based on present data in customer and order fixtures
     *
     * @param Order $fixture
     * @return void
     */
    public function fillAddresses(Order $fixture)
    {
        $this->getShippingAddressBlock()->uncheckSameAsBillingShippingAddress();
        $this->getTemplateBlock()->waitLoader();
        $billingAddress = $fixture->getBillingAddress();
        if (empty($billingAddress)) {
            $this->getBillingAddressBlock()->fill($fixture->getCustomer()->getDefaultBillingAddress());
        } else {
            $this->getBillingAddressBlock()->fill($billingAddress);
        }
        $this->getShippingAddressBlock()->setSameAsBillingShippingAddress();
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Select shipping method
     *
     * @param Order $fixture
     * @return void
     */
    public function selectShippingMethod(Order $fixture)
    {
        $this->getShippingMethodBlock()->selectShippingMethod($fixture);
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Select payment method
     *
     * @param Order $fixture
     * @return void
     */
    public function selectPaymentMethod(Order $fixture)
    {
        $this->getBillingMethodBlock()->selectPaymentMethod($fixture);
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Submit order
     *
     * @return void
     */
    public function submitOrder()
    {
        $this->getTotalsBlock()->submitOrder();
    }
}
