<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Email_Template_FilterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Email_Template_Filter
     */
    protected $_model = null;

    protected function setUp()
    {
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Email_Template_Filter');
    }

    /**
     * Isolation level has been raised in order to flush themes configuration in-memory cache
     */
    public function testViewDirective()
    {
        $url = $this->_model->viewDirective(array(
            '{{view url="Magento_Page::favicon.ico"}}',
            'view',
            ' url="Magento_Page::favicon.ico"', // note leading space
        ));
        $this->assertStringEndsWith('favicon.ico', $url);
    }

    /**
     * @magentoConfigFixture current_store web/unsecure/base_link_url http://example.com/
     */
    public function testStoreDirective()
    {
        $url = $this->_model->storeDirective(array(
            '{{store direct_url="arbitrary_url/"}}',
            'store',
            ' direct_url="arbitrary_url/"',
        ));
        $this->assertStringMatchesFormat('http://example.com/%sarbitrary_url/', $url);

        $url = $this->_model->storeDirective(array(
            '{{store url="core/ajax/translate"}}',
            'store',
            ' url="core/ajax/translate"',
        ));
        $this->assertStringMatchesFormat('http://example.com/%score/ajax/translate/', $url);
    }

    public function testEscapehtmlDirective()
    {
        $this->_model->setVariables(array(
            'first' => '<p><i>Hello</i> <b>world!</b></p>',
            'second' => '<p>Hello <strong>world!</strong></p>',
        ));

        $allowedTags = 'i,b';

        $expectedResults = array(
            'first' => '&lt;p&gt;<i>Hello</i> <b>world!</b>&lt;/p&gt;',
            'second' => '&lt;p&gt;Hello &lt;strong&gt;world!&lt;/strong&gt;&lt;/p&gt;'
        );

        foreach ($expectedResults as $varName => $expectedResult) {
            $result = $this->_model->escapehtmlDirective(array(
                '{{escapehtml var=$' . $varName . ' allowed_tags=' . $allowedTags . '}}',
                'escapehtml',
                ' var=$' . $varName . ' allowed_tags=' . $allowedTags
            ));
            $this->assertEquals($expectedResult, $result);
        }
    }

    /**
     * @magentoDataFixture Magento/Core/Model/Email/_files/themes.php
     * @magentoAppIsolation enabled
     * @dataProvider layoutDirectiveDataProvider
     *
     * @param string $area
     * @param string $directiveParams
     * @param string $expectedOutput
     */
    public function testLayoutDirective($area, $directiveParams, $expectedOutput)
    {
        Magento_TestFramework_Helper_Bootstrap::getInstance()->reinitialize(array(
            Mage::PARAM_APP_DIRS => array(
                Magento_Core_Model_Dir::THEMES => dirname(__DIR__) . '/_files/design'
            )
        ));

        $collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Resource_Theme_Collection');
        $themeId = $collection->getThemeByFullPath('frontend/test_default')->getId();
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface')
            ->getStore()->setConfig(Magento_Core_Model_View_Design::XML_PATH_THEME_ID, $themeId);

        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $themes = array('frontend' => 'test_default', 'adminhtml' => 'test_default');
        $design = $objectManager->create('Magento_Core_Model_View_Design', array('themes' => $themes));
        $objectManager->addSharedInstance($design, 'Magento_Core_Model_View_Design');

        /** @var $layout Magento_Core_Model_Layout */
        $layout = $objectManager->create('Magento_Core_Model_Layout', array('area' => $area));
        $objectManager->addSharedInstance($layout, 'Magento_Core_Model_Layout');
        $this->assertEquals($area, $layout->getArea());
        $this->assertEquals(
            $area,
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')->getArea()
        );
        $objectManager->get('Magento_Core_Model_View_DesignInterface')->setDesignTheme('test_default');

        $actualOutput = $this->_model->layoutDirective(array(
            '{{layout ' . $directiveParams . '}}',
            'layout',
            ' ' . $directiveParams,
        ));
        $this->assertEquals($expectedOutput, trim($actualOutput));
    }

    /**
     * @return array
     */
    public function layoutDirectiveDataProvider()
    {
        $result = array(
            /* if the area parameter is omitted, frontend layout updates are used regardless of the current area */
            'area parameter - omitted' => array(
                'adminhtml',
                'handle="email_template_test_handle"',
                'E-mail content for frontend/test_default theme',
            ),
            'area parameter - frontend' => array(
                'adminhtml',
                'handle="email_template_test_handle" area="frontend"',
                'E-mail content for frontend/test_default theme',
            ),
            'area parameter - backend' => array(
                'frontend',
                'handle="email_template_test_handle" area="adminhtml"',
                'E-mail content for adminhtml/test_default theme',
            ),
            'custom parameter' => array(
                'frontend',
                'handle="email_template_test_handle" template="sample_email_content_custom.phtml"',
                'Custom E-mail content for frontend/test_default theme',
            ),
        );
        return $result;
    }
}
