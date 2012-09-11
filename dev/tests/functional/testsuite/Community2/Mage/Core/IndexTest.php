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
 * Verification of Core Index controller pages
 */
class Community2_Mage_Core_IndexTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Verify that going to frontend Core index page returns empty result<p>
     * <p>Steps to reproduce:<p>
     * <p>1. Navigate to the Core index page<p>
     * <p>Expected result:</p>
     * <p>Empty page with empty body</p>
     *
     * @test
     */
    public function testIndexEmpty()
    {
        //Steps
        $this->navigate('core_index_page');
        //Verifying
        $xpath = $this->_getControlXpath('pageelement', 'any_body_element');
        $numElements = $this->getXpathCount($xpath);
        $this->assertEquals(0, $numElements, 'The body of Core index page must be empty');
    }
}
