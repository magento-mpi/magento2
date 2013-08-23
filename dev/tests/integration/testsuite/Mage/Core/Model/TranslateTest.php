<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoDataFixture Mage/Adminhtml/controllers/_files/cache/all_types_disabled.php
 */
class Mage_Core_Model_TranslateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Translate
     */
    protected $_model;

    /**
     * @var Mage_Core_Model_View_DesignInterface
     */
    protected $_designModel;

    /**
     * @var Mage_Core_Model_View_FileSystem
     */
    protected $_viewFileSystem;

    public function setUp()
    {
        $pathChunks = array(dirname(__FILE__), '_files', 'design', 'frontend', 'test_default', 'locale', 'en_US',
            'translate.csv');

        $this->_viewFileSystem = $this->getMock('Mage_Core_Model_View_FileSystem',
            array('getLocaleFileName', 'getDesignTheme'), array(), '', false);


        $this->_viewFileSystem->expects($this->any())
            ->method('getLocaleFileName')
            ->will($this->returnValue(implode(DIRECTORY_SEPARATOR, $pathChunks)));

        $theme = $this->getMock('Mage_Core_Model_Theme', array('getId', 'getCollection'), array(), '', false);
        $theme->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(10));

        $collection = $this->getMock('Mage_Core_Model_Theme', array('getThemeByFullPath'), array(), '', false);
        $collection->expects($this->any())
            ->method('getThemeByFullPath')
            ->will($this->returnValue($theme));

        $theme->expects($this->any())
            ->method('getCollection')
            ->will($this->returnValue($collection));

        $this->_viewFileSystem->expects($this->any())
            ->method('getDesignTheme')
            ->will($this->returnValue($theme));

        Mage::getObjectManager()->addSharedInstance($this->_viewFileSystem, 'Mage_Core_Model_View_FileSystem');

        Mage::getConfig()->setModuleDir('Mage_Core', 'locale', dirname(__FILE__) . '/_files/Mage/Core/locale');
        Mage::getConfig()->setModuleDir('Mage_Catalog', 'locale', dirname(__FILE__) . '/_files/Mage/Catalog/locale');

        $this->_designModel = $this->getMock('Mage_Core_Model_View_Design',
            array('getDesignTheme'),
            array(
                Mage::getSingleton('Mage_Core_Model_StoreManagerInterface'),
                Mage::getSingleton('Mage_Core_Model_Theme_FlyweightFactory')
            )
        );

        $this->_designModel->expects($this->any())
            ->method('getDesignTheme')
            ->will($this->returnValue($theme));

        Mage::getObjectManager()->addSharedInstance($this->_designModel, 'Mage_Core_Model_View_Design');

        $this->_model = Mage::getModel('Mage_Core_Model_Translate');
        $this->_model->init(Mage_Core_Model_App_Area::AREA_FRONTEND);
    }

    /**
     * @magentoDataFixture Mage/Core/_files/db_translate.php
     * @magentoDataFixture Mage/Adminhtml/controllers/_files/cache/all_types_enabled.php
     */
    public function testInitCaching()
    {
        // ensure string translation is cached
        $this->_model->init(Mage_Core_Model_App_Area::AREA_FRONTEND, null);

        /** @var Mage_Core_Model_Resource_Translate_String $translateString */
        $translateString = Mage::getModel('Mage_Core_Model_Resource_Translate_String');
        $translateString->saveTranslate('Fixture String', 'New Db Translation');

        $this->_model->init(Mage_Core_Model_App_Area::AREA_FRONTEND, null);
        $this->assertEquals(
            'Fixture Db Translation', $this->_model->translate(array('Fixture String')),
            'Translation is expected to be cached'
        );

        $this->_model->init(Mage_Core_Model_App_Area::AREA_FRONTEND, null, true);
        $this->assertEquals(
            'New Db Translation', $this->_model->translate(array('Fixture String')),
            'Forced load should not use cache'
        );
    }

    public function testGetModulesConfig()
    {
        /** @var $modulesConfig Mage_Core_Model_Config_Element */
        $modulesConfig = $this->_model->getModulesConfig();

        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $modulesConfig);

        /* Number of nodes is the number of enabled modules, that support translation */
        $checkedNode = 'Mage_Core';
        $this->assertGreaterThan(1, count($modulesConfig));
        $this->assertNotEmpty($modulesConfig->$checkedNode);
        $this->assertXmlStringEqualsXmlString(
            '<Mage_Core>
                <files>
                    <default>Mage_Core.csv</default>
                    <fixture>../../../../../../dev/tests/integration/testsuite/Mage/Core/_files/fixture.csv</fixture>
                </files>
            </Mage_Core>',
            $modulesConfig->$checkedNode->asXML()
        );

        $this->_model->init('non_existing_area', null, true);
        $this->assertEquals(array(), $this->_model->getModulesConfig());
    }

    public function testGetConfig()
    {
        $this->assertEquals('frontend', $this->_model->getConfig(Mage_Core_Model_Translate::CONFIG_KEY_AREA));
        $this->assertEquals('en_US', $this->_model->getConfig(Mage_Core_Model_Translate::CONFIG_KEY_LOCALE));
        $this->assertEquals(1, $this->_model->getConfig(Mage_Core_Model_Translate::CONFIG_KEY_STORE));
        $this->assertEquals(Mage::getDesign()->getDesignTheme()->getId(),
            $this->_model->getConfig(Mage_Core_Model_Translate::CONFIG_KEY_DESIGN_THEME));
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
        $this->assertInstanceOf('Mage_Core_Model_Resource_Translate', $this->_model->getResource());
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
        $this->_model = Mage::getModel('Mage_Core_Model_Translate');
        $this->_model->init(Mage_Core_Model_App_Area::AREA_FRONTEND);

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
                'Text with different translation on different modules'
            ),
            array(
                Mage::getModel('Mage_Core_Model_Translate_Expr', array(
                    'text'   => 'Text with different translation on different modules',
                    'module' => 'Mage_Core'
                )),
                'Text translation by Mage_Core module'
            ),
            array(
                Mage::getModel('Mage_Core_Model_Translate_Expr', array(
                    'text'   => 'Text with different translation on different modules',
                    'module' => 'Mage_Catalog'
                )),
                'Text translation by Mage_Catalog module'
            ),
            array(
                Mage::getModel('Mage_Core_Model_Translate_Expr', array('text' => 'text_with_no_translation')),
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
