<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tax
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product Tax class Core_Mage_creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tax_ProductTaxClass_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Rule name for clean up</p>
     */
    protected $_ruleToBeDeleted = null;

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Sales->Tax->Product Tax Classes</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_tax_rule');
    }

    /**
     * <p>Remove Tax rule after test</p>
     */
    protected function tearDownAfterTest()
    {
        if (!empty($this->_ruleToBeDeleted)) {
            $this->loginAdminUser();
            $this->navigate('manage_tax_rule');
            $this->taxHelper()->deleteTaxItem($this->_ruleToBeDeleted, 'rule');
            $this->_ruleToBeDeleted = null;
        }
    }

    /**
     * <p>Need to verify that product tax class are created by default and displayed in the field.</p>
     *
     * @test
     * @testlinkId TL-MAGE-6380
     */
    public function productTaxClassByDefault()
    {
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->assertTrue($this->verifyCompositeMultiselect('product_tax_class', array('Taxable Goods')),
            '"Taxable Goods" is absent or not selected');
    }

    /**
     * <p>Need verified that admin user will be able to create any Product Tax Class.</p>
     *
     * @test
     * @testLinkId TL-MAGE-6381
     */
    public function creatingCustomerTaxClass()
    {
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $productTaxClass = $this->generate('string', 26);
        $this->fillCompositeMultiselect('product_tax_class', array($productTaxClass));
        $this->assertTrue($this->verifyCompositeMultiselect('product_tax_class', array($productTaxClass)),
            'Failed to add new value');
        return $productTaxClass;
    }

    /**
     * <p>Need verified that admin user will be not able to create any Customer Tax Class with same name.</p>
     *
     * @test
     * @depends creatingCustomerTaxClass
     * @testLinkId TL-MAGE-6389
     */
    public function creatingWithSameName()
    {
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $productTaxClass = $this->generate('string', 26);
        $this->addCompositeMultiselectValue('product_tax_class', $productTaxClass);
        $this->addCompositeMultiselectValue('product_tax_class', $productTaxClass, null, false);
        $this->assertTrue($this->alertIsPresent(), 'No validation alert');
        $alertText = $this->alertText();
        $this->acceptAlert();
        $this->assertEquals($this->_getMessageXpath('tax_class_exists'), $alertText);
    }

    /**
     * <p>Need verified that admin user will be not able to create any Product Tax Class with no name.</p>
     *
     * @test
     * @depends creatingCustomerTaxClass
     * @testLinkId TL-MAGE-6383
     */
    public function creatingWithEmptyName()
    {
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->setExpectedException('RuntimeException');
        $this->addCompositeMultiselectValue('product_tax_class', null, null, false);
    }

    /**
     * <p>Need verified that admin user will be able to edit any Product Tax Class.</p>
     *
     * @test
     * @depends creatingCustomerTaxClass
     * @testLinkId TL-MAGE-6384
     */
    public function editingProductTaxClass()
    {
        $this->markTestSkipped('Skip due to bug');
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $productTaxClass = $this->generate('string', 26);
        $newProductTaxClass = $this->generate('string', 26);
        $containerXpath = $this->_getControlXpath('composite_multiselect', 'product_tax_class');
        $this->addCompositeMultiselectValue(null, $productTaxClass, $containerXpath);
        $this->editCompositeMultiselectOption(null, $productTaxClass, $newProductTaxClass, $containerXpath);
        $this->addParameter('optionName', $newProductTaxClass);
        $elementXpath = $this->_getControlXpath('pageelement', 'multiselect_option');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->assertTrue((bool)$this->elementIsPresent($containerXpath . $elementXpath),
            'Tax class is not saved');
    }

    /**
     * <p>Need verified that admin user will be able to delete any Product Tax Class.</p>
     *
     * @test
     * @param string $productTaxClass
     * @depends creatingCustomerTaxClass
     * @testLinkId TL-MAGE-6385
     */
    public function deleteProductTaxClass($productTaxClass)
    {
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->deleteCompositeMultiselectOption('product_tax_class', $productTaxClass, 'confirmation_for_delete_class');
    }

    /**
     * <p>Need verify that  Product Tax Class should not deleted if it is used in Tax Rule.</p>
     *
     * @test
     * @depends creatingCustomerTaxClass
     * @testLinkId TL-MAGE-6386
     */
    public function deleteUsedProductTaxClass()
    {
        $productTaxClass = $this->generate('string', 26);
        //Create tax class
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->addCompositeMultiselectValue('product_tax_class', $productTaxClass);

        //Create/open tax rule
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required',
            array('product_tax_class' => $productTaxClass));
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->createTaxItem($taxRuleData, 'rule');
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        $searchTaxRuleData = $this->loadDataSet('Tax', 'search_tax_rule', array('filter_name' => $taxRuleData['name']));
        $this->_ruleToBeDeleted = $searchTaxRuleData;

        //delete tax class
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $containerXpath = $this->_getControlXpath('composite_multiselect', 'product_tax_class');
        $labelLocator = "//div[normalize-space(label/span)='$productTaxClass']";
        $generalElement = $this->getElement($containerXpath);
        $optionElement = $this->getChildElement($generalElement, $labelLocator);
        $optionElement->click();
        $this->getChildElement($optionElement, "//span[@title='Delete']")->click();
        //First message
        $this->assertTrue($this->alertIsPresent(), 'There is no confirmation message');
        $alertText = $this->alertText();
        $this->acceptAlert();
        $this->assertSame($this->_getMessageXpath('confirmation_for_delete_class'), $alertText,
            'Confirmation message is incorrect');
        //Second message
        $this->assertTrue($this->alertIsPresent(), 'There is no confirmation message');
        $alertText = $this->alertText();
        $this->acceptAlert();
        $this->assertSame($this->_getMessageXpath('delete_error_notice'), $alertText,
            'Confirmation message is incorrect');
    }

    /**
     * <p>Fails because of MAGE-5237</p>
     *
     * @test
     * @depends creatingCustomerTaxClass
     * @param string $specialValue
     * @dataProvider withSpecialValuesDataProvider
     */
    public function withSpecialValues($specialValue)
    {
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->fillCompositeMultiselect('product_tax_class', array($specialValue));
        $this->assertTrue($this->verifyCompositeMultiselect('product_tax_class', array($specialValue)),
            'Failed to add new value');
    }

    /**
     * Data provider for withSpecialValues() test
     *
     * @return array
     */
    public function withSpecialValuesDataProvider()
    {
        return array(
            array($this->generate('string', 255)),
            array($this->generate('string', 50, ':punct:'))
        );
    }
}