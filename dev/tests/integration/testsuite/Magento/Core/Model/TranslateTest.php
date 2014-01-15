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

namespace Magento\Core\Model;

/**
 * @magentoDataFixture Magento/Backend/controllers/_files/cache/all_types_disabled.php
 */
class TranslateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $_model;

    /**
     * @var \Magento\View\DesignInterface
     */
    protected $_designModel;

    /**
     * @var \Magento\View\FileSystem
     */
    protected $_viewFileSystem;

    protected function setUp()
    {
        $pathChunks = array(__DIR__, '_files', 'design', 'frontend', 'test_default', 'i18n', 'en_US.csv');

        $this->_viewFileSystem = $this->getMock('Magento\View\FileSystem',
            array('getFilename', 'getDesignTheme'), array(), '', false);

        $this->_viewFileSystem->expects($this->any())
            ->method('getFilename')
            ->will($this->returnValue(implode('/', $pathChunks)));

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

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->addSharedInstance($this->_viewFileSystem, 'Magento\View\FileSystem');

        /** @var $moduleReader \Magento\Module\Dir\Reader */
        $moduleReader = $objectManager->get('Magento\Module\Dir\Reader');
        $moduleReader->setModuleDir('Magento_Core', 'i18n', __DIR__ . '/_files/Magento/Core/i18n');
        $moduleReader->setModuleDir('Magento_Catalog', 'i18n',
            __DIR__ . '/_files/Magento/Catalog/i18n');

        /** @var \Magento\Core\Model\View\Design _designModel */
        $this->_designModel = $this->getMock('Magento\Core\Model\View\Design',
            array('getDesignTheme'),
            array(
                $objectManager->get('Magento\Core\Model\StoreManagerInterface'),
                $objectManager->get('Magento\View\Design\Theme\FlyweightFactory'),
                $objectManager->get('Magento\Core\Model\Config'),
                $objectManager->get('Magento\Core\Model\Store\Config'),
                $objectManager->get('Magento\Core\Model\ThemeFactory'),
                $objectManager->get('Magento\Core\Model\App'),
                $objectManager->get('Magento\App\State'),
                array('frontend' => 'test_default')
            )
        );

        $this->_designModel->expects($this->any())
            ->method('getDesignTheme')
            ->will($this->returnValue($theme));

        $objectManager->addSharedInstance($this->_designModel, 'Magento\Core\Model\View\Design\Proxy');

        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Translate');
        $this->_model->init(\Magento\Core\Model\App\Area::AREA_FRONTEND);
    }

    /**
     * @magentoDataFixture Magento/Core/_files/db_translate.php
     * @magentoDataFixture Magento/Backend/controllers/_files/cache/all_types_enabled.php
     * @covers \Magento\Backend\Model\Resource\Translate::_getStoreId
     * @covers \Magento\Backend\Model\Resource\Translate\String::_getStoreId
     */
    public function testInitCaching()
    {
        // ensure string translation is cached
        $this->_model->init(\Magento\Core\Model\App\Area::AREA_FRONTEND, null);

        /** @var \Magento\Core\Model\Resource\Translate\String $translateString */
        $translateString = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Resource\Translate\String');
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

    public function testGetConfig()
    {
        $this->assertEquals('frontend', $this->_model->getConfig(\Magento\Core\Model\Translate::CONFIG_KEY_AREA));
        $this->assertEquals('en_US', $this->_model->getConfig(\Magento\Core\Model\Translate::CONFIG_KEY_LOCALE));
        $this->assertEquals(1, $this->_model->getConfig(\Magento\Core\Model\Translate::CONFIG_KEY_STORE));
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\View\DesignInterface');
        $this->assertEquals($design->getDesignTheme()->getId(),
            $this->_model->getConfig(\Magento\Core\Model\Translate::CONFIG_KEY_DESIGN_THEME));
        $this->assertNull($this->_model->getConfig('non_existing_key'));
    }

    public function testGetData()
    {
        $this->markTestIncomplete('Bug MAGETWO-6986');
        $expectedData = include(__DIR__ . '/Translate/_files/_translation_data.php');
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
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Translate');
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
