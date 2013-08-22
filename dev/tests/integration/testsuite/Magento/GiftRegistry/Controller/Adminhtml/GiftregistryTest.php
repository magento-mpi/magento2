<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_GiftRegistry_Controller_Adminhtml_GiftregistryTest extends Magento_Test_TestCase_ControllerAbstract
{
    protected function setUp()
    {
        parent::setUp();
        /** @var $auth Magento_Backend_Model_Auth */
        Mage::getSingleton('Magento_Backend_Model_Url')->turnOffSecretKey();
        $auth = Mage::getSingleton('Magento_Backend_Model_Auth');
        $auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
    }

    protected function tearDown()
    {
        /** @var $auth Magento_Backend_Model_Auth */
        $auth = Mage::getSingleton('Magento_Backend_Model_Auth');
        $auth->logout();
        Mage::getSingleton('Magento_Backend_Model_Url')->turnOnSecretKey();
        parent::tearDown();
    }

    public function testNewAction()
    {
        $this->dispatch('backend/admin/giftregistry/new');
        $this->assertRegExp('/<h1 class\="title">\s*New Gift Registry Type\s*<\/h1>/',
            $this->getResponse()->getBody()
        );
        $this->assertContains('<a href="#magento_giftregistry_tabs_general_section_content"'
                . ' id="magento_giftregistry_tabs_general_section" name="general_section"'
                . ' title="General Information"', $this->getResponse()->getBody()
        );
        $this->assertContains('<a href="#magento_giftregistry_tabs_registry_attributes_content"'
                . ' id="magento_giftregistry_tabs_registry_attributes"'
                . ' name="registry_attributes" title="Attributes"', $this->getResponse()->getBody()
        );
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveAction()
    {
        $this->getRequest()->setPost('type', array(
            'code'       => 'test_registry',
            'label'      => 'Test',
            'sort_order' => 10,
            'is_listed'  => 1,
        ));
        $this->dispatch('backend/admin/giftregistry/save/store/0');
        /** @var $type Magento_GiftRegistry_Model_Type */
        $type = Mage::getModel('Magento_GiftRegistry_Model_Type');
        $type->setStoreId(0);

        $type = $type->load('test_registry', 'code');

        $this->assertInstanceOf('Magento_GiftRegistry_Model_Type', $type);
        $this->assertNotEmpty($type->getId());
    }
}
