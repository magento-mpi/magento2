<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PrintedTemplate_Model_Wysiwyg_ConfigTest extends Magento_Test_TestCase_ControllerAbstract
{
    protected function setUp()
    {
        parent::setUp();
        Mage::app()->loadAreaPart(Magento_Core_Model_App_Area::AREA_ADMINHTML, Magento_Core_Model_App_Area::PART_CONFIG);
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

    /**
     * @covers Saas_PrintedTemplate_Model_Wysiwyg_Config
     */
    public function testGetConfig()
    {
        $configModel = Mage::getModel('Saas_PrintedTemplate_Model_Wysiwyg_Config');

        $this->dispatch('backend/admin/template/edit/entity_type/invoice');
        $result = $this->getResponse()->getBody();

        $expectedFonts = 'var editorFonts = \'' . $configModel->getFonts() . '\'';
        $this->assertContains($expectedFonts, $result);

        $expectedErrorMessage = 'editor.settings.magentoheader_error_message = \''
            . $configModel->getHeaderErrorMessage() . '\'';
        $this->assertContains($expectedErrorMessage, $result);

        $expectedErrorMessage = 'editor.settings.magentofooter_error_message = \''
            . $configModel->getFooterErrorMessage() . '\'';
        $this->assertContains($expectedErrorMessage, $result);
    }
}
