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

class Mage_DesignEditor_EditorControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * @param string $handle
     * @param string $expectedMessage
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     * @dataProvider pageActionErrorDataProvider
     */
    public function testPageActionError($handle, $expectedMessage)
    {
        $this->getRequest()->setParam('handle', $handle);
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
            'no handle type'      => array('', 'Invalid page handle specified.'),
            'invalid handle'      => array('1nvalid_handle', 'Invalid page handle specified.'),
            'non-existing handle' => array(
                'non_existing_handle', 'Specified page type or page fragment type doesn\'t exist: %s'
            ),
        );
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     * @dataProvider pageActionDataProvider
     *
     * @param string $handle
     * @param string $requiredModule
     */
    public function testPageAction($handle, $requiredModule)
    {
        if (!in_array($requiredModule, Magento_Test_Helper_Factory::getHelper('config')->getEnabledModules())) {
            $this->markTestSkipped("Test requires the module '$requiredModule' to be enabled.");
        }
        $this->getRequest()->setParam('handle', $handle);
        $this->dispatch('design/editor/page');
        $this->assertEquals(200, $this->getResponse()->getHttpResponseCode());
        $controller = Mage::app()->getFrontController()->getAction();
        $this->assertInstanceOf('Mage_DesignEditor_EditorController', $controller);
        $this->assertContains(
            'data-selected="li[rel=\'' . $handle . '\']"',
            $this->getResponse()->getBody(),
            'Page type control should maintain the selection of the current page handle.'
        );
    }

    public function pageActionDataProvider()
    {
        return array(
            'Catalog Product View'             => array('catalog_product_view',            'Mage_Catalog'),
            'One Page Checkout Overview'       => array('checkout_onepage_review',         'Mage_Checkout'),
            'Paypal Express Review Details'    => array('paypal_express_review_details',   'Mage_Paypal'),
            'Paypal UK Express Review Details' => array('paypaluk_express_review_details', 'Mage_PaypalUk'),
        );
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
    public function testThemeAction()
    {
        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        $this->getRequest()->setParam('theme_id', $session->getThemeId());
        $this->dispatch('design/editor/theme');
        $this->assertRedirect();

        $theme = new Mage_Core_Model_Theme();
        $theme->load($session->getThemeId());

        $this->assertEquals('default/default_blank', $theme->getThemePath());
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testThemeActionWrongValue()
    {
        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        $this->getRequest()->setParam('theme_id', $session->getThemeId());
        $this->dispatch('design/editor/theme');
        $this->assertRedirect();

        $theme = new Mage_Core_Model_Theme();
        $theme->load($session->getThemeId());

        $this->assertNotEquals('wrong/theme/applied', $theme->getThemePath());
    }

    public function testThemeActionNonActivatedEditor()
    {
        $this->getRequest()->setParam('theme_id', 0);
        $this->dispatch('design/editor/theme');
        $this->assert404NotFound();

        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');

        $theme = new Mage_Core_Model_Theme();
        $theme->load($session->getThemeId());

        $this->assertNotEquals('default/default_blank', $theme->getThemePath());
    }

    /**
     * @magentoDataFixture Mage/DesignEditor/_files/design_editor_active.php
     */
    public function testThemeActionRedirectUrl()
    {
        $expectedRedirectUrl = 'http://localhost/index.php/path/to/redirect/?value=1#anchor';

        $this->getRequest()->setParam(
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED,
            Mage::helper('Mage_Core_Helper_Data')->urlEncode($expectedRedirectUrl)
        );
        $this->dispatch('design/editor/theme');
        $this->assertRedirect($this->equalTo($expectedRedirectUrl));
    }
}
