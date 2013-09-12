<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Authorizenet_Controller_Directpost_PaymentTest extends Magento_TestFramework_TestCase_ControllerAbstract
{
    public function testResponseActionValidationFiled()
    {
        $this->getRequest()->setPost('controller_action_name', 'onepage');
        $this->dispatch('authorizenet/directpost_payment/response');
        // @codingStandardsIgnoreStart
        $this->assertContains(
            'authorizenet/directpost_payment/redirect/success/0/error_msg/The transaction was declined because the response hash validation failed.',
        // @codingStandardsIgnoreEnd
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
