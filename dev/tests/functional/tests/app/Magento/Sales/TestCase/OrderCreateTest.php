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

namespace Magento\Sales\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;
use Magento\Catalog\Test\Fixture\Product;

class OrderCreateTest extends Functional
{
    /**
     * Products for order
     *
     * @var array
     */
    protected $_products;

    public function testCreateOrder()
    {
        $this->_createPreconditions();

        $this->_proceedToOrderCreatePage();

        $this->_fillOrderData();

        //TODO: verify order on backend order page
        //TODO: verify customer on backend customers page
    }

    protected function _createPreconditions()
    {
        //Taxes
        $configFixture = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $configFixture->switchData('default_tax_config');
        $configFixture->persist();
        Factory::getApp()->magentoTaxRemoveTaxRule();
        $taxRule = Factory::getFixtureFactory()->getMagentoTaxTaxRule();
        $taxRule->switchData('custom_rule');
        $taxRule->persist();

        //Shipping
        $configFixture = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $configFixture->switchData('flat_rate');
        $configFixture->persist();

        //Products
        $simple = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simple->persist();
        $this->_products = array(
            $simple
        );
    }

    protected function _proceedToOrderCreatePage()
    {
        $orderGridPage = Factory::getPageFactory()->getAdminSalesOrder();
        $gridPageActionsBlock = $orderGridPage->getPageActionsBlock();

        Factory::getApp()->magentoBackendLoginUser();
        $orderGridPage->open();
        $gridPageActionsBlock->addNew();
    }

    protected function _fillOrderData()
    {
        $billingAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $billingAddress->switchData('backend_address_US_1');
        $orderCreatePage = Factory::getPageFactory()->getAdminSalesOrderCreateIndex();
        $customerSelectionGrid = $orderCreatePage->getOrderCustomerBlock();
        $itemsOrderedGrid = $orderCreatePage->getItemsOrderedGrid();
        $productsAddGrid = $orderCreatePage->getItemsAddGrid();
        $billingAddressForm = $orderCreatePage->getBillingAddressForm();
        $shippingAddressForm = $orderCreatePage->getShippingAddressForm();

        //TODO: make possible to avoid creating new and select existing customer instead
        $customerSelectionGrid->createNewCustomer();
        $itemsOrderedGrid->addNewProduct();

        /** @var $product Product */
        foreach ($this->_products as $product)
        {
            $productsAddGrid->searchAndSelect(array(
                'sku' => $product->getProductSku()
            ));
        }
        $productsAddGrid->addSelectedProducts();

        $billingAddressForm->fill($billingAddress);
        $shippingAddressForm->setSameAsBillingShippingAddress();

        //TODO: payment&shipping

        //TODO: submit order
    }
}
