<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Cardgate
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Cardgate_CardgateControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @test
     */
    public function testControlActionValidationFailed()
    {
        ob_clean();
        $this->dispatch('cardgate/cardgate/control');
        $result = ob_get_clean();
        $this->assertContains('Callback hash validation failed!', $result);
    }

    public function testRedirectActionErrorMessage()
    {
        $this->getRequest()->setParam('success', '0');
        $this->getRequest()->setParam('error_msg', 'Error message');
        $this->dispatch('authorizenet/directpost_payment/redirect');
        $this->assertContains(
            'alert("Error message");',
            $this->getResponse()->getBody()
        );
    }

}
