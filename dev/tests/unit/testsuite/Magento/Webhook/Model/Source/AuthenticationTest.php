<?php
/**
 * Magento_Webhook_Model_Source_Authentication
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Source_AuthenticationTest extends Magento_Webhook_Model_Source_Pkg
{
    public function testGetAuthenticationsForForm()
    {
        $unitUnderTest = new Magento_Webhook_Model_Source_Authentication(array('type' => 'blah'));
        $elements = $unitUnderTest->getAuthenticationsForForm();
        $this->_assertElements($elements);

        // Verify that we return cached results
        $secondResult = $unitUnderTest->getAuthenticationsForForm();
        $this->assertEquals($elements, $secondResult);
    }
}
