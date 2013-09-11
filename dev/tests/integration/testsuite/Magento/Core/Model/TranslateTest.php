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

/**
 * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_disabled.php
 */
class Magento_Core_Model_TranslateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_View_DesignInterface
     */
    protected $_designModel;

    /**
     * @var Magento_Core_Model_View_FileSystem
     */
    protected $_viewFileSystem;

    public function setUp()
    {
        $pathChunks = array(dirname(__FILE__), '_files', 'design', 'frontend', 'test_default', 'locale', 'en_US',
            'translate.csv');

        $this->_viewFileSystem = $this->getMock('Magento_Core_Model_View_FileSystem',
            array('getLocaleFileName', 'getDesignTheme'), array(), '', false);


        $this->_viewFileSystem->expects($this->any())
            ->method('getLocaleFileName')
            ->will($this->returnValue(implode(DIRECTORY_SEPARATOR, $pathChunks)));

        $theme = $this->getMock('Magento_Core_Model_Theme', array('getId', 'getCollection'), array(), '', false);
        $theme->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(10));

        $collection = $this->getMock('Magento_Core_Model_Theme', array('getThemeByFullPath'), array(), '', false);
        $collection->expects($this->any())
            ->method('getThemeByFullPath')
            ->will($this->returnValue($theme));

        $theme->expects($this->any())
            ->method('getCollection')
            ->will($this->returnValue($collection));

        $this->_viewFileSystem->expects($this->any())
            ->method('getDesignTheme')
            ->will($this->returnValue($theme));

        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->addSharedInstance($this->_viewFileSystem, 'Magento_Core_Model_View_FileSystem');

        $objectManager->get('Magento_Core_Model_Config')
            ->setModuleDir('Magento_Core', 'locale', dirname(__FILE__) . '/_files/Magento/Core/locale');
        $objectManager->get('Magento_Core_Model_Config')->setModuleDir('Magento_Catalog', 'locale',
            dirname(__FILE__) . '/_files/Magento/Catalog/locale');

        $this->_designModel = $this->getMock('Magento_Core_Model_View_Design',
            array('getDesignTheme'),
            array(
                Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_StoreManagerInterface'),
                Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Theme_FlyweightFactory'),
                Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Store_Config'),
                Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config')
            )
        );

        $this->_designModel->expects($this->any())
            ->method('getDesignTheme')
            ->will($this->returnValue($theme));

        $objectManager->addSharedInstance($this->_designModel, 'Magento_Core_Model_View_Design');

        $this->_model = Mage::getModel('Magento_Core_Model_Translate');
        $this->_model->init(Magento_Core_Model_App_Area::AREA_FRONTEND);
    }

    /**
     * @magentoDataFixture Magento/Core/_files/db_translate.php
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_enabled.php
     */
    public function testInitCaching()
    {
        // ensure string translation is cached
        $this->_model->init(Magento_Core_Model_App_Area::AREA_FRONTEND, null);

        /** @var Magento_Core_Model_Resource_Translate_String $translateString */
        $translateString = Mage::getModel('Magento_Core_Model_Resource_Translate_String');
        $translateString->saveTranslate('Fixture String', 'New Db Translation');

        $this->_model->init(Magento_Core_Model_App_Area::AREA_FRONTEND, null);
        $this->assertEquals(
            'Fixture Db Translation', $this->_model->translate(array('Fixture String')),
            'Translation is expected to be cached'
        );

        $this->_model->init(Magento_Core_Model_App_Area::AREA_FRONTEND, null, true);
        $this->assertEquals(
            'New Db Translation', $this->_model->translate(array('Fixture String')),
            'Forced load should not use cache'
        );
    }

    public function testGetModulesConfig()
    {
        /** @var $modulesConfig Magento_Core_Model_Config_Element */
        $modulesConfig = $this->_model->getModulesConfig();

        $this->assertInstanceOf('Magento_Core_Model_Config_Element', $modulesConfig);

        /* Number of nodes is the number of enabled modules, that support translation */
        $checkedNode = 'Magento_Core';
        $this->assertGreaterThan(1, count($modulesConfig));
        $this->assertNotEmpty($modulesConfig->$checkedNode);
        $this->assertXmlStringEqualsXmlString(
            '<Magento_Core>
                <files>
                    <default>Magento_Core.csv</default>
                    <fixture>../../../../../../dev/tests/integration/testsuite/Magento/Core/_files/fixture.csv</fixture>
                </files>
            </Magento_Core>',
            $modulesConfig->$checkedNode->asXML()
        );

        $this->_model->init('non_existing_area', null, true);
        $this->assertEquals(array(), $this->_model->getModulesConfig());
    }

    public function testGetConfig()
    {
        $this->assertEquals('frontend', $this->_model->getConfig(Magento_Core_Model_Translate::CONFIG_KEY_AREA));
        $this->assertEquals('en_US', $this->_model->getConfig(Magento_Core_Model_Translate::CONFIG_KEY_LOCALE));
        $this->assertEquals(1, $this->_model->getConfig(Magento_Core_Model_Translate::CONFIG_KEY_STORE));
        $design = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_View_DesignInterface');
        $this->assertEquals($design->getDesignTheme()->getId(),
            $this->_model->getConfig(Magento_Core_Model_Translate::CONFIG_KEY_DESIGN_THEME));
        $this->assertNull($this->_model->getConfig('non_existing_key'));
    }

    public function testGetData()
    {
        $this->markTestIncomplete('Bug MAGETWO-6986');
        $expectedData = include(dirname(__FILE__) . '/Translate/_files/_translation_data.php');
        $this->assertEquals($expectedData, $this->_model->getData());
    }

    public function testGetSetLocale()
    {
        $this->assertEquals('en_US', $this->_model->getLocale());
        $this->_model->setLocale('ru_RU');
        $this->assertEquals('ru_RU', $this->_model->getLocale());
    }

    public function testGetResource()
    {
        $this->assertInstanceOf('Magento_Core_Model_Resource_Translate', $this->_model->getResource());
    }

    public function testGetTranslate()
    {
        $translate = $this->_model->getTranslate();
        $this->assertInstanceOf('Zend_Translate', $translate);
    }

    /**
     * @magentoAppIsolation enabled
     * @dataProvider translateDataProvider
     */
    public function testTranslate($inputText, $expectedTranslation)
    {
        $this->_model = Mage::getModel('Magento_Core_Model_Translate');
        $this->_model->init(Magento_Core_Model_App_Area::AREA_FRONTEND);

        $actualTranslation = $this->_model->translate(array($inputText));
        $this->assertEquals($expectedTranslation, $actualTranslation);
    }

    /**
     * @return array
     */
    public function translateDataProvider()
    {
        return array(
            array('', ''),
            array(
                'Text with different translation on different modules',
                'Text translation that was last loaded'
            ),
            array(
                'text_with_no_translation',
                'text_with_no_translation'
            ),
            array(
                'Design value to translate',
                'Design translated value'
            )
        );
    }

    public function testGetSetTranslateInline()
    {
        $this->assertEquals(true, $this->_model->getTranslateInline());
        $this->_model->setTranslateInline(false);
        $this->assertEquals(false, $this->_model->getTranslateInline());
    }
}
