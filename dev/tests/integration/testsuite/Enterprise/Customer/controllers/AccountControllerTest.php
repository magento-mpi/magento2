<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_Customer_AccountController
 */
class Enterprise_Customer_AccountControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    public function testCreateAction()
    {
        $this->dispatch('customer/account/create');
        $html = $this->getResponse()->getBody();
        $this->assertContains('<ul class="form-list">', $html);
    }
}
