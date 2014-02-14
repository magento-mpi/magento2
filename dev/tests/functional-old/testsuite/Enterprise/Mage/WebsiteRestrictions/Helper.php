<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_WebsiteRestrictions
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_WebsiteRestrictions_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Validates Frontend Http Code
     *
     * @param string $page
     * @param string $code
     */
    public function validateFrontendHttpCode($page, $code)
    {
        $url = $this->getPageUrl('frontend', $page);
        $httpResponse = $this->getHttpResponse($url);
        $this->assertEquals($code, $httpResponse['http_code'], 'Wrong http response code');
    }
}