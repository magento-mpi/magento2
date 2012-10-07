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
     * <p>Preconditions:</p>
     * <p>Navigate to Sales->Tax->Product Tax Classes</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_product_tax_class');
    }

    /**
     * <p>Creating Product Tax class Core_Mage_with required field</p>
     * <p>Steps</p>
     * <p>1. Click "Add New" button </p>
     * <p>2. Fill in required fields</p>
     * <p>3. Click "Save Class" button</p>
     * <p>Expected Result:</p>
     * <p>Product Tax class Core_Mage_created, success message appears</p>
     *
     * @return array $productTaxClassData
     * @test
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $productTaxClassData = $this->loadDataSet('Tax', 'new_product_tax_class');
        //Steps
        $this->taxHelper()->createTaxItem($productTaxClassData, 'product_class');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_class');
        $this->taxHelper()->openTaxItem($productTaxClassData, 'product_class');
        $this->assertTrue($this->verifyForm($productTaxClassData), $this->getParsedMessages());

        return $productTaxClassData;
    }

    /**
     * <p>Creating Product Tax class Core_Mage_with name that exists</p>
     * <p>Steps</p>
     * <p>1. Click "Add New" button </p>
     * <p>2. Fill in class Core_Mage_Name with value that exists</p>
     * <p>3. Click "Save Class" button</p>
     * <p>Expected Result:</p>
     * <p>Product Tax class Core_Mage_should not be created, error message appears</p>
     *
     * @param array $productTaxClassData
     *
     * @test
     * @depends withRequiredFieldsOnly
     */
    public function withNameThatAlreadyExists($productTaxClassData)
    {
        //Steps
        $this->taxHelper()->createTaxItem($productTaxClassData, 'product_class');
        //Verifying
        $this->assertMessagePresent('error', 'tax_class_exists');
    }

    /**
     * <p>Creating Product Tax class Core_Mage_with empty name</p>
     * <p>Steps</p>
     * <p>1. Click "Add New" button </p>
     * <p>2. Leave class Core_Mage_Name empty</p>
     * <p>3. Click "Save Class" button</p>
     * <p>Expected Result:</p>
     * <p>Product Tax class Core_Mage_should not be created, error message appears</p>
     *
     * @depends withRequiredFieldsOnly
     * @test
     */
    public function withEmptyName()
    {
        //Data
        $productTaxClassData = $this->loadDataSet('Tax', 'new_product_tax_class', array('product_class_name' => ''));
        //Steps
        $this->taxHelper()->createTaxItem($productTaxClassData, 'product_class');
        //Verifying
        $this->assertMessagePresent('error', 'empty_class_name');
    }

    /**
     * Fails because of MAGE-5237
     * <p>Creating a new Product Tax class Core_Mage_with special values (long, special chars).</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New"</p>
     * <p>2. Fill in the fields</p>
     * <p>3. Click button "Save Class"</p>
     * <p>4. Open the Tax Class</p>
     * <p>Expected result:</p>
     * <p>All fields has the same values.</p>
     *
     * @param string $specialValue
     *
     * @test
     * @dataProvider withSpecialValuesDataProvider
     */
    public function withSpecialValues($specialValue)
    {
        //Data
        $taxClass = $this->loadDataSet('Tax', 'new_product_tax_class', array('product_class_name' => $specialValue));
        //Steps
        $this->taxHelper()->createTaxItem($taxClass, 'product_class');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_class');
        //Steps
        $this->taxHelper()->openTaxItem($taxClass, 'product_class');
        //Verifying
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