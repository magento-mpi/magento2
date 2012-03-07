<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/Admin/_files/user.php
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Customer_GroupControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    protected function setUp()
    {
        parent::setUp();
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOffSecretKey();
        $this->_session = new Mage_Admin_Model_Session();
        $this->_session->login('user', 'password');
    }

    public function testNewAction()
    {
        $this->dispatch('admin/customer_group/new');
        $responseBody = $this->getResponse()->getBody();
        $this->assertStringMatchesFormat('%a<div class="content-header">%ANew Customer Group%a', $responseBody);
    }
}
