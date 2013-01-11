<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_PrintedTemplate
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Adminhtml_TemplateControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    protected function setUp()
    {
        parent::setUp();
        Mage::setCurrentArea('adminhtml');
        /** @var $auth Mage_Backend_Model_Auth */
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOffSecretKey();
        $auth = Mage::getSingleton('Mage_Backend_Model_Auth');
        $auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
    }

    protected function tearDown()
    {
        /** @var $auth Mage_Backend_Model_Auth */
        $auth = Mage::getSingleton('Mage_Backend_Model_Auth');
        $auth->logout();
        Mage::getSingleton('Mage_Backend_Model_Url')->turnOnSecretKey();
        parent::tearDown();
    }

    public function testPrintedTemplateIsInstalled()
    {
        $this->dispatch('backend/admin/template/index');

        $this->assertInstanceOf(
            'Saas_PrintedTemplate_Block_Adminhtml_Template_Grid',
            Mage::app()->getLayout()->getBlock('printed.template.grid'),
            'Saas_PrintedTemplate_Block_Adminhtml_Template_Grid block is not loaded'
        );

        $result = $this->getResponse()->getBody();
        $expected = 'Please make sure that popups are allowed.';
        $this->assertContains(
            $expected,
            $result,
            'Saas_PrintedTemplate_Block_Adminhtml_Template_Grid block is not rendered'
        );
    }
}
