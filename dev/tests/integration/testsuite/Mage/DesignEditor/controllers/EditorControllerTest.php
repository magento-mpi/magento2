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
    public function testPreDispatchSession()
    {
        $this->dispatch('design/editor/page');
        $this->assert404NotFound();
    }

    /**
     * @param string $pageType
     * @param string $expectedMessage
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     * @dataProvider pageActionErrorDataProvider
     */
    public function testPageActionError($pageType, $expectedMessage)
    {
        $this->getRequest()->setParam('page_type', $pageType);
        $this->dispatch('design/editor/page');
        $this->assertEquals(503, $this->getResponse()->getHttpResponseCode());
        $this->assertStringMatchesFormat($expectedMessage, $this->getResponse()->getBody());
    }

    /**
     * @return array
     */
    public function pageActionErrorDataProvider()
    {
        return array(
            'no page type'      => array('', 'Invalid page type specified.'),
            'invalid page type' => array('invalid_type', 'Invalid page type specified.'),
            'no-nexisting type' => array('non_existing_type', 'Specified page type doesn\'t exist: %s'),
        );
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testPageAction()
    {
        $this->getRequest()->setParam('page_type', 'catalog_product_view');
        $this->dispatch('design/editor/page');
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
        $controller = Mage::app()->getFrontController()->getAction();
        $this->assertInstanceOf('Mage_DesignEditor_EditorController', $controller);
    }

    public function testGetFullActionName()
    {
        $this->dispatch('design/editor/page');
        $controller = Mage::app()->getFrontController()->getAction();
        $this->assertNotInstanceOf('Mage_DesignEditor_EditorController', $controller);
    }

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
