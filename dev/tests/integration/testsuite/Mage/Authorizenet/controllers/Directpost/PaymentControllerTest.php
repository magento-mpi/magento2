<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Authorizenet
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Authorizenet_Directpost_PaymentControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testResponseActionValidationFiled()
    {
        $this->getRequest()->setPost('controller_action_name', 'onepage');
        $this->dispatch('authorizenet/directpost_payment/response');
        $this->assertContains(
            'authorizenet/directpost_payment/redirect/success/0/error_msg/Response hash validation failed.'
                . ' Transaction declined.',
            $this->getResponse()->getBody()
        );
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
