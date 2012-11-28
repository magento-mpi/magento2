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
class Core_Mage_Core_IndexTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Verify that going to frontend Core index page returns empty result<p>
     *
     * @test
     */
    public function testIndexEmpty()
    {
        //Steps
        $this->frontend();
        $this->navigate('core_index_page');
        //Verifying
        $this->assertEquals(0, $this->getControlCount('pageelement', 'any_body_element'),
            'The body of Core index page must be empty');
    }
}