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
 * Customer Tax class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tax_CustomerTaxClass_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Sales-Tax-Customer Tax Classes</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_tax_rule');
    }

    /**
     * <p>Customer Tax Class by default</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6387
     */
    public function checkDefaultValues()
    {
        $multiselect = 'customer_tax_class';
        $this->clickButton('add_rule');
        $this->clickControl('link','tax_rule_info_additional_link');
        $this->assertTrue($this->verifyCompositeMultiselect($multiselect, array('Retail Customer')));
    }

    /**
     * <p>Creating Customer Tax Class</p>
     *
     * @test
     * @depends checkDefaultValues
     * @TestlinkId TL-MAGE-6388
     */
    public function createCustomerTaxClass()
    {
        $multiselect = 'customer_tax_class';
        $this->clickButton('add_rule');
        $this->clickControl('link','tax_rule_info_additional_link');
        $taxClassName = $this->generate('string', 20);
        $this->fillCompositeMultiselect($multiselect, array($taxClassName));
        $this->assertTrue($this->verifyCompositeMultiselect($multiselect, array($taxClassName)),
            'Failed to add new value');
    }

    /**
     * <p>Creating Customer Tax Class</p>
     *
     * @test
     * @depends checkDefaultValues
     * @TestlinkId TL-MAGE-6389
     */
    public function withNameThatAlreadyExists()
    {
        $multiselect = 'customer_tax_class';
        $this->clickButton('add_rule');
        $this->clickControl('link','tax_rule_info_additional_link');
        $taxClassName = $this->generate('string', 20);
        $this->fillCompositeMultiselect($multiselect, $taxClassName);
        $this->verifyCompositeMultiselect($multiselect, $taxClassName);
        $this->addCompositeMultiselectValue($multiselect, $taxClassName, null, false);
        $this->waitUntil(function ($testCase) {
            /** @var Mage_Selenium_TestCase $testCase */
            $testCase->alertText();
            return true;
        }, 5);
        $alertText = $this->alertText();
        $this->acceptAlert();
        $this->assertEquals($this->_getMessageXpath('tax_class_exists'), $alertText);
    }

    /**
     * <p>Creating Customer Tax class with empty name</p>
     *
     * @test
     * @depends checkDefaultValues
     * @TestlinkId TL-MAGE-6390
     */
    public function withEmptyName()
    {
        $this->clickButton('add_rule');
        $this->clickControl('link','tax_rule_info_additional_link');
        $this->setExpectedException('RuntimeException');
        $this->addCompositeMultiselectValue('customer_tax_class', '', null, false);
    }

    /**
     * <p>Edit existing Customer tax Class</p>
     *
     * @test
     * @depends checkDefaultValues
     * @TestlinkId TL-MAGE-6391
     */
    public function editExistingValue()
    {
        $multiselect = 'customer_tax_class';
        $this->clickButton('add_rule');
        $this->clickControl('link','tax_rule_info_additional_link');
        $taxClassName = $this->generate('string', 10);
        $this->fillCompositeMultiselect($multiselect, $taxClassName);
        $this->assertTrue($this->verifyCompositeMultiselect($multiselect, $taxClassName));
        $newTaxName = $this->generate('string', 10);
        $this->editCompositeMultiselectOption($multiselect, $taxClassName, $newTaxName);
        $this->assertTrue($this->verifyCompositeMultiselect($multiselect, $newTaxName));
    }

    /**
     * <p>Creating a new Customer Tax class with special values (long, special chars).</p>
     *
     * @param string $specialValue
     *
     * @test
     * @dataProvider withSpecialValuesDataProvider
     */
    public function withSpecialValues($specialValue)
    {
        $this->markTestIncomplete('MAGETWO-8436, MAGETWO-9100, MAGETWO-9098');
        $multiselect = 'customer_tax_class';
        $this->clickButton('add_rule');
        $this->clickControl('link','tax_rule_info_additional_link');
        $this->fillCompositeMultiselect($multiselect, $specialValue);
        $this->assertTrue($this->verifyCompositeMultiselect($multiselect, $specialValue), 'Failed to add new value');
    }

    /**
     * Data provider for special values for Tax Class
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