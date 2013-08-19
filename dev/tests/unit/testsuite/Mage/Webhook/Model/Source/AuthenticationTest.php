<?php
/**
 * Mage_Webhook_Model_Source_Authentication
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Source_AuthenticationTest extends Mage_Webhook_Model_Source_Pkg
{
    public function testGetAuthenticationsForForm()
    {
        $unitUnderTest = new Mage_Webhook_Model_Source_Authentication(array('type' => 'blah'));
        $elements = $unitUnderTest->getAuthenticationsForForm();
        $this->_assertElements($elements);

        // Verify that we return cached results
        $secondResult = $unitUnderTest->getAuthenticationsForForm();
        $this->assertEquals($elements, $secondResult);
    }
}
