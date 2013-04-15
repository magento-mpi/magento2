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
 * Product Tax class Core_Mage_deletion tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tax_ProductTaxClass_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Save rule name for clean up</p>
     */
    protected $_ruleToBeDeleted = array();

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Sales-Tax-Product Tax Classes</p>
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
            $this->_ruleToBeDeleted = array();
        }
    }

    /**
    * <p>Need verified that admin user will be able to delete any Product Tax Class.</p>
    *
    * @test
    * @TestLinkId TL-MAGE-6385
    */
    public function deleteProductTaxClass()
    {
        $taxClass = $this->generate('string', 26);
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->fillCompositeMultiselect('product_tax_class', array($taxClass));
        $this->assertTrue($this->verifyCompositeMultiselect('product_tax_class', array($taxClass)),
            $this->getParsedMessages());
        $this->deleteCompositeMultiselectOption('product_tax_class', $taxClass, 'confirmation_for_delete_class');
    }

   /**
    * <p>Need verify that  Product Tax Class should not deleted if it is used in Tax Rule.</p>
    *
    * @test
    * @depends deleteProductTaxClass
    * @TestLinkId TL-MAGE-6386
    */
    public function deleteUsedProductTaxClass()
    {
        $taxClass = $this->loadDataSet('Tax', 'new_product_tax_class');
        //Create tax class
        $this->taxHelper()->createTaxClass($taxClass);

        //Create tax rule
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required',
            array('product_tax_class' => $taxClass['product_tax_class']));
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->createTaxRule($taxRuleData);
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        $this->_ruleToBeDeleted = $this->loadDataSet('Tax', 'search_tax_rule',
            array('filter_name' => $taxRuleData['name']));
        $this->taxHelper()->deleteTaxClass($taxClass['product_tax_class'], 'product_tax_class', 'used_in_rule_error');
   }

    /**
     * <p>Delete a Product Tax class that used in Product</p>
     *
     * @test
     * @depends deleteProductTaxClass
     */
    public function usedInProduct()
    {
        //Data
        $taxClass = $this->loadDataSet('Tax', 'new_product_tax_class');
        $product = $this->loadDataSet('Product', 'simple_product_required',
            array('general_tax_class' => $taxClass['product_tax_class'])
        );
        //Steps
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->createTaxClass($taxClass);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->navigate('manage_tax_rule');
        $this->taxHelper()
            ->deleteTaxClass($taxClass['product_tax_class'], 'product_tax_class', 'used_in_product_error');
    }
}
