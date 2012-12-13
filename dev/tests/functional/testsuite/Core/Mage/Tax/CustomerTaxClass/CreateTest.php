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
 * Customer Tax class Core_Mage_creation tests
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
        $this->navigate('manage_customer_tax_class');
    }

    /**
     * <p>Creating Customer Tax class Core_Mage_with required field</p>
     *
     * @return array $customerTaxClassData
     * @test
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $customerTaxClassData = $this->loadDataSet('Tax', 'new_customer_tax_class');
        //Steps
        $this->taxHelper()->createTaxItem($customerTaxClassData, 'customer_class');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_class');
        //Steps
        $this->taxHelper()->openTaxItem($customerTaxClassData, 'customer_class');
        //Verifying
        $this->assertTrue($this->verifyForm($customerTaxClassData), $this->getParsedMessages());

        return $customerTaxClassData;
    }

    /**
     * <p>Creating Customer Tax class Core_Mage_with name that exists</p>
     *
     * @param array $customerTaxClassData
     *
     * @test
     * @depends withRequiredFieldsOnly
     */
    public function withNameThatAlreadyExists($customerTaxClassData)
    {
        //Steps
        $this->taxHelper()->createTaxItem($customerTaxClassData, 'customer_class');
        //Verifying
        $this->assertMessagePresent('error', 'tax_class_exists');
    }

    /**
     * <p>Creating Customer Tax class Core_Mage_with empty name</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     */
    public function withEmptyName()
    {
        //Data
        $customerTaxClassData = $this->loadDataSet('Tax', 'new_customer_tax_class', array('customer_class_name' => ''));
        //Steps
        $this->taxHelper()->createTaxItem($customerTaxClassData, 'customer_class');
        //Verifying
        $this->assertMessagePresent('error', 'empty_class_name');
    }

    /**
     * Fails because of MAGE-5237
     * <p>Creating a new Customer Tax class Core_Mage_with special values (long, special chars).</p>
     *
     * @param string $specialValue
     *
     * @test
     * @dataProvider withSpecialValuesDataProvider
     */
    public function withSpecialValues($specialValue)
    {
        //Data
        $taxClass = $this->loadDataSet('Tax', 'new_customer_tax_class', array('customer_class_name' => $specialValue));
        //Steps
        $this->taxHelper()->createTaxItem($taxClass, 'customer_class');
        $this->assertMessagePresent('success', 'success_saved_tax_class');
        //Verifying
        $this->taxHelper()->openTaxItem($taxClass, 'customer_class');
        $this->assertTrue($this->verifyForm($taxClass), $this->getParsedMessages());
    }

    public function withSpecialValuesDataProvider()
    {
        return array(
            array($this->generate('string', 255)),
            array($this->generate('string', 50, ':punct:'))
        );
    }
}