<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Block\Catalog\Product;

use Magento\Customer\Controller\RegistryConstants;

/**
 * Tests Magento\Downloadable\Block\Catalog\Product\Links.php
 *
 */
class LinksTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\TestFramework\ObjectManager */
    private $objectManager;

    /** @var \Magento\Tax\Model\Calculation */
    private $taxCalculationModel;

    /** @var \Magento\Framework\Registry */
    private $registry;

    /** @var \Magento\Downloadable\Block\Catalog\Product\Links */
    private $linksBlock;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->registry = $this->objectManager->get('Magento\Framework\Registry');
        $this->taxCalculationModel = $this->objectManager->create('Magento\Tax\Model\Calculation');

        $this->linksBlock = $this->objectManager->get('Magento\Framework\View\LayoutInterface')
            ->createBlock('Magento\Downloadable\Block\Catalog\Product\Links');
    }

    public function tearDown()
    {
        $this->registry->unregister('product');
        $this->registry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
        $this->registry->unregister('_fixture/Magento_Tax_Model_Calculation_Rule');
        $this->registry->unregister('_fixture/Magento_Tax_Model_Calculation_Rate');
    }

    /**
     * Test that has no customer registered.
     *
     * @magentoDataFixture Magento/Downloadable/_files/product_with_files.php
     */
    public function testGetFormattedLinkPriceNoCustomer()
    {
        $product = $this->objectManager->create('Magento\Catalog\Model\Product')->load(1);
        $this->registry->register('product', $product);
        $link = array_values($this->linksBlock->getLinks())[0];
        $formattedLink = $this->linksBlock->getFormattedLinkPrice($link);
        $this->assertEquals('<span class="price-notice">+<span class="price">$15.00</span></span>', $formattedLink);
    }

    /**
     * Test that uses customer's billing address as tax calculation base.
     *
     * @magentoConfigFixture current_store tax/display/type 3
     * @magentoConfigFixture current_store tax/calculation/based_on billing
     * @magentoDataFixture Magento/Downloadable/_files/product_with_files.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testGetFormattedLinkPriceCustomerBasedTax()
    {
        /** set the product and tax classes from tax_class fixture */
        $this->setUpTaxClasses();
        $link = array_values($this->linksBlock->getLinks())[0];
        $formattedLink = $this->linksBlock->getFormattedLinkPrice($link);
        $this->assertEquals(
            '<span class="price-notice">+<span class="price">$15.00</span>'.
            ' (+<span class="price">$16.13</span> Incl. Tax)</span>',
            $formattedLink
        );
    }

    /**
     * Test a customer outside of region.
     *
     * @magentoConfigFixture current_store tax/display/type 3
     * @magentoConfigFixture current_store tax/calculation/based_on billing
     * @magentoDataFixture Magento/Downloadable/_files/product_with_files.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     * @magentoDataFixture Magento/Tax/_files/tax_classes.php
     */
    public function testGetFormattedLinkPriceCustomerBasedTaxDiffRegion()
    {
        /** set the product and tax classes from tax_class fixture */
        $this->setUpTaxClasses(13);
        $link = array_values($this->linksBlock->getLinks())[0];
        $formattedLink = $this->linksBlock->getFormattedLinkPrice($link);
        $this->assertEquals('<span class="price-notice">+<span class="price">$15.00</span></span>', $formattedLink);
    }

    /**
     * Test that has a customer but product based tax.
     *
     * @magentoConfigFixture current_store tax/display/type 3
     * @magentoDataFixture Magento/Downloadable/_files/product_with_files.php
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetFormattedLinkPriceCustomerProductTax()
    {
        $product = $this->objectManager->create('Magento\Catalog\Model\Product')->load(1);
        $product->setTaxPercent(10);
        $product->save();

        $this->registry->register('product', $product);
        $this->registry->register(RegistryConstants::CURRENT_CUSTOMER_ID, 1);

        $link = array_values($this->linksBlock->getLinks())[0];
        $formattedLink = $this->linksBlock->getFormattedLinkPrice($link);
        $this->assertEquals(
            '<span class="price-notice">+<span class="price">$15.00</span>'.
            ' (+<span class="price">$16.50</span> Incl. Tax)</span>',
            $formattedLink
        );
    }

    /**
     * Set the product and tax classes from tax_class fixture
     *
     * @param int $addressRegionId  Region to use for customer billing address.
     *                Defaults to 12 which is the same as in tax rate fixture
     */
    private function setUpTaxClasses($addressRegionId = 12)
    {
        $taxRule = $this->registry->registry('_fixture/Magento_Tax_Model_Calculation_Rule');
        $customerTaxClasses = $taxRule->getTaxCustomerClass();
        $productTaxClasses = $taxRule->getTaxProductClass();

        $customerGroup = $this->objectManager->create('Magento\Customer\Model\Group')->load(1);
        $customerGroup->setTaxClassId($customerTaxClasses[0]);
        $customerGroup->save();

        $address = $this->objectManager->create('Magento\Customer\Model\Address')->load(1);
        $address->setRegionId($addressRegionId);
        $address->save();

        $product = $this->objectManager->create('Magento\Catalog\Model\Product')->load(1);
        $product->setTaxClassId($productTaxClasses[0]);
        $product->save();

        $this->registry->register('product', $product);
        $this->registry->register(RegistryConstants::CURRENT_CUSTOMER_ID, 1);
    }
}
 