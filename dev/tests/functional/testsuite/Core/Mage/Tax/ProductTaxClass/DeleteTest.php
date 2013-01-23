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

    protected function tearDownAfterTest()
    {
        //Remove Tax rule after test
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
    * @testLinkId TL-MAGE-6385
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
    * @testLinkId TL-MAGE-6386
    */
    public function deleteUsedProductTaxClass()
    {
        $taxClass = $this->generate('string', 26);
        //Create tax class
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->addCompositeMultiselectValue('product_tax_class', $taxClass);

        //Create tax rule
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required',
            array('product_tax_class' => $taxClass));
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->createTaxItem($taxRuleData, 'rule');
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        $this->_ruleToBeDeleted = $this->loadDataSet('Tax', 'search_tax_rule',
            array('filter_name' => $taxRuleData['name']));
        $this->_deleteTaxItem($taxClass, 'used_in_rule_error');
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
        $taxClass = $this->generate('string', 26);
        $product = $this->loadDataSet('Product', 'simple_product_required',
            array('prices_tax_class' => $taxClass));
        //Steps
        $this->navigate('manage_tax_rule');
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->addCompositeMultiselectValue('product_tax_class', $taxClass);
        //Steps
        $this->navigate('manage_products');
        //@TODO: Remove when $this->productHelper()->saveProduct() will work properly with half screen window
        $this->currentWindow()->maximize();
        $this->productHelper()->createProduct($product);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->navigate('manage_tax_rule');
        $this->_deleteTaxItem($taxClass, 'used_in_product_error');
    }

    /**
     * Helper method
     *
     * @param $optionLabel
     * @param $msg
     */
    private function _deleteTaxItem($optionLabel, $msg)
    {
        //delete tax class
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $containerXpath = $this->_getControlXpath('composite_multiselect', 'product_tax_class');
        $labelLocator = "//div[normalize-space(label/span)='$optionLabel']";
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
        $this->assertSame($this->_getMessageXpath($msg), $alertText, 'Confirmation message is incorrect');
    }
}