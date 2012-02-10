<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_DesignEditor
 */
class Mage_DesignEditor_EditorControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testSkinAction()
    {
        $this->getRequest()->setParam('skin', 'default/default/blank');
        $this->dispatch('design/editor/skin');
        $this->assertRedirect();

        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        $this->assertEquals('default/default/blank', $session->getSkin());
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testSkinActionWrongValue()
    {
        $this->getRequest()->setParam('skin', 'wrong/skin/applied');
        $this->dispatch('design/editor/skin');
        $this->assertRedirect();

        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        $this->assertNotEquals('wrong/skin/applied', $session->getSkin());
    }

    public function testSkinActionNonActivatedEditor()
    {
        $this->getRequest()->setParam('skin', 'default/default/blank');
        $this->dispatch('design/editor/skin');
        $this->assert404NotFound();

        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        $this->assertNotEquals('default/default/blank', $session->getSkin());
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testSkinActionRedirectUrl()
    {
        $expectedRedirectUrl = 'http://localhost/index.php/path/to/redirect/?value=1#anchor';

        $this->getRequest()->setParam('skin', 'default/default/blank');
        $this->getRequest()->setParam(
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED,
            Mage::helper('Mage_Core_Helper_Data')->urlEncode($expectedRedirectUrl)
        );
        $this->dispatch('design/editor/skin');
        $this->assertRedirect($expectedRedirectUrl);
    }
}
