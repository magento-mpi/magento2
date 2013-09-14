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
     * @var \Magento\Core\Model\Translate
     */
    protected $_model;

    /**
     * @var \Magento\Core\Model\View\DesignInterface
     */
    protected $_designModel;

    /**
     * @var \Magento\Core\Model\View\FileSystem
     */
    protected $_viewFileSystem;

    public function setUp()
    {
        $pathChunks = array(dirname(__FILE__), '_files', 'design', 'frontend', 'test_default', 'locale', 'en_US',
            'translate.csv');

        $this->_viewFileSystem = $this->getMock('Magento\Core\Model\View\FileSystem',
            array('getLocaleFileName', 'getDesignTheme'), array(), '', false);


        $this->_viewFileSystem->expects($this->any())
            ->method('getLocaleFileName')
            ->will($this->returnValue(implode(DIRECTORY_SEPARATOR, $pathChunks)));

        $theme = $this->getMock('Magento\Core\Model\Theme', array('getId', 'getCollection'), array(), '', false);
        $theme->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(10));

        $collection = $this->getMock('Magento\Core\Model\Theme', array('getThemeByFullPath'), array(), '', false);
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
        $objectManager->addSharedInstance($this->_viewFileSystem, 'Magento\Core\Model\View\FileSystem');

        Mage::getConfig()->setModuleDir('Magento_Core', 'locale', dirname(__FILE__) . '/_files/Magento/Core/locale');
        Mage::getConfig()->setModuleDir('Magento_Catalog', 'locale',
            dirname(__FILE__) . '/_files/Magento/Catalog/locale');

        $this->_designModel = $this->getMock('Magento\Core\Model\View\Design',
            array('getDesignTheme'),
            array(
                Mage::getSingleton('Magento\Core\Model\StoreManagerInterface'),
                Mage::getSingleton('Magento\Core\Model\Theme\FlyweightFactory')
            )
        );

        $this->_designModel->expects($this->any())
            ->method('getDesignTheme')
            ->will($this->returnValue($theme));

        $objectManager->addSharedInstance($this->_designModel, 'Magento\Core\Model\View\Design');

        $this->_model = Mage::getModel('Magento\Core\Model\Translate');
        $this->_model->init(\Magento\Core\Model\App\Area::AREA_FRONTEND);
    }

    /**
     * @magentoDataFixture Magento/Core/_files/db_translate.php
     * @magentoDataFixture Magento/Adminhtml/controllers/_files/cache/all_types_enabled.php
     */
    public function testInitCaching()
    {
        // ensure string translation is cached
        $this->_model->init(\Magento\Core\Model\App\Area::AREA_FRONTEND, null);

        /** @var \Magento\Core\Model\Resource\Translate\String $translateString */
        $translateString = Mage::getModel('Magento\Core\Model\Resource\Translate\String');
        $translateString->saveTranslate('Fixture String', 'New Db Translation');

        $this->_model->init(\Magento\Core\Model\App\Area::AREA_FRONTEND, null);
        $this->assertEquals(
            'Fixture Db Translation', $this->_model->translate(array('Fixture String')),
            'Translation is expected to be cached'
        );

        $this->_model->init(\Magento\Core\Model\App\Area::AREA_FRONTEND, null, true);
        $this->assertEquals(
            'New Db Translation', $this->_model->translate(array('Fixture String')),
            'Forced load should not use cache'
        );
    }

    public function testGetModulesConfig()
    {
        /** @var $modulesConfig \Magento\Core\Model\Config\Element */
        $modulesConfig = $this->_model->getModulesConfig();

        $this->assertInstanceOf('Magento\Core\Model\Config\Element', $modulesConfig);

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
        $this->assertEquals('frontend', $this->_model->getConfig(\Magento\Core\Model\Translate::CONFIG_KEY_AREA));
        $this->assertEquals('en_US', $this->_model->getConfig(\Magento\Core\Model\Translate::CONFIG_KEY_LOCALE));
        $this->assertEquals(1, $this->_model->getConfig(\Magento\Core\Model\Translate::CONFIG_KEY_STORE));
        $design = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\View\DesignInterface');
        $this->assertEquals($design->getDesignTheme()->getId(),
            $this->_model->getConfig(\Magento\Core\Model\Translate::CONFIG_KEY_DESIGN_THEME));
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
        $this->assertInstanceOf('Magento\Core\Model\Resource\Translate', $this->_model->getResource());
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
        $this->_model = Mage::getModel('Magento\Core\Model\Translate');
        $this->_model->init(\Magento\Core\Model\App\Area::AREA_FRONTEND);

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
