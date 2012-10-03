<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_UrlRewrite
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Verification of Url Rewrite backend functionality
 */
class Community2_Mage_UrlRewrite_FormTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Verify that url rewrite form is present at the backend page<p>
     * <p>Steps to reproduce:<p>
     * <p>1. Navigate to the form to add new custom url rewrite<p>
     * <p>Expected result:</p>
     * <p>A form to enter url rewrite is present</p>
     * <p>Only one instance of such form is present</p>
     * <p>The form has proper attributes (matched by uimap xpath)<p>
     *
     * @test
     */
    public function testFormIsPresent()
    {
        //Steps
        $this->navigate('new_url_rewrite');
        //Verifying
        $xpath = $this->_getControlXpath('pageelement', 'form');
        $numForms = $this->getXpathCount($xpath);
        $this->assertEquals(1, $numForms, 'There must be present one form to enter url rewrite data');
    }
}
