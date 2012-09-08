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
 *
 */

/**
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_UrlRewrite_FormTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * Verify that url rewrite form is present at the admin page
     *
     * Steps to reproduce:
     * 1. Navigate to the form to add new url rewrite
     * 2. Verify, that there is one and only one form to enter url rewrite (and it matches by attributes according
     *    to uimap xpath)
     *
     * @test
     */
    public function testFormIsPresent()
    {
        $this->navigate('new_url_rewrite');
        $xpath = $this->_getControlXpath('pageelement', 'form');
        $numForms = $this->getXpathCount($xpath);
        $this->assertEquals(1, $numForms, 'There must be present one form to enter url rewrite data');
    }
}
