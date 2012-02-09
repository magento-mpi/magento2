<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_DesignEditor
 *
 * @magentoDataFixture Mage/Admin/_files/user.php
 */
class Mage_Adminhtml_Sales_OrderControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    protected $_session = null;

    public function setUp()
    {
        parent::setUp();
        Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOffSecretKey();
        $this->_session = new Mage_Admin_Model_Session;
        $this->_session->login('user', 'password');
    }

    public function testIndexAction()
    {
        $this->dispatch('admin/sales_order/index');
        $this->assertContains('Total 0 records found', $this->getResponse()->getBody());
    }

    /**
     * @magentoDataFixture Mage/Sales/_files/order.php
     * @magentoDataFixture Mage/Admin/_files/user.php
     */
    public function testIndexActionWithOrder()
    {
        $this->dispatch('admin/sales_order/index');
        $this->assertContains('Total 1 records found', $this->getResponse()->getBody());
    }
}
