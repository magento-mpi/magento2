<?php
/**
 * \Magento\Webhook\Model\Source\Authentication
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Source;

class AuthenticationTest extends \Magento\Webhook\Model\Source\Pkg
{
    public function testGetAuthenticationsForForm()
    {
        $unitUnderTest = new \Magento\Webhook\Model\Source\Authentication(array('type' => 'blah'));
        $elements = $unitUnderTest->getAuthenticationsForForm();
        $this->_assertElements($elements);

        // Verify that we return cached results
        $secondResult = $unitUnderTest->getAuthenticationsForForm();
        $this->assertEquals($elements, $secondResult);
    }
}
