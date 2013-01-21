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
     * <p>Save tax class name for clean up</p>
     */
    protected $_taxClass = null;

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
     * Clean up
     */
    protected function tearDownAfterTest()
    {
        //Remove Tax class after test
        if (!empty($this->_taxClass)) {
            $this->loginAdminUser();
            $this->navigate('manage_tax_rule');
            $this->clickButton('add_rule');
            $this->clickControl('link', 'tax_rule_info_additional_link');
            $this->deleteCompositeMultiselectOption('product_tax_class', $this->_taxClass,
                'confirmation_for_delete_class');
            $this->_taxClass = null;
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
        $taxClass = $this->generate('string', 26);
        $this->fillCompositeMultiselect('product_tax_class', array($taxClass));
        $this->assertTrue($this->verifyCompositeMultiselect('product_tax_class', array($taxClass)),
            'Failed to add new value');
        $this->_taxClass = $taxClass;
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
        $taxClass = $this->generate('string', 26);
        $this->addCompositeMultiselectValue('product_tax_class', $taxClass);
        $this->addCompositeMultiselectValue('product_tax_class', $taxClass, null, false);
        $this->assertTrue($this->alertIsPresent(), 'No validation alert');
        $alertText = $this->alertText();
        $this->acceptAlert();
        $this->assertEquals($this->_getMessageXpath('tax_class_exists'), $alertText);
        $this->_taxClass = $taxClass;
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
        $taxClass = $this->generate('string', 26);
        $newProductTaxClass = $this->generate('string', 26);
        $containerXpath = $this->_getControlXpath('composite_multiselect', 'product_tax_class');
        $this->addCompositeMultiselectValue(null, $taxClass, $containerXpath);
        $this->editCompositeMultiselectOption(null, $taxClass, $newProductTaxClass, $containerXpath);
        $this->addParameter('optionName', $newProductTaxClass);
        $elementXpath = $this->_getControlXpath('pageelement', 'multiselect_option');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->assertTrue((bool)$this->elementIsPresent($containerXpath . $elementXpath),
            'Tax class is not saved');
        $this->_taxClass = $newProductTaxClass;
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
        $this->_taxClass = $specialValue;
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