<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoCache all 0
 */
class TranslateTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        /** @var \Magento\Framework\View\FileSystem $viewFileSystem */
        $viewFileSystem = $this->getMock(
            'Magento\Framework\View\FileSystem',
            array('getLocaleFileName', 'getDesignTheme'),
            array(),
            '',
            false
        );

        $viewFileSystem->expects($this->any())
            ->method('getLocaleFileName')
            ->will(
                $this->returnValue(dirname(__DIR__) . '/Core/Model/_files/design/frontend/test_default/i18n/en_US.csv')
            );

        /** @var \Magento\Framework\View\Design\ThemeInterface $theme */
        $theme = $this->getMock('Magento\Framework\View\Design\ThemeInterface', array());
        $theme->expects($this->any())->method('getId')->will($this->returnValue(10));

        $viewFileSystem->expects($this->any())->method('getDesignTheme')->will($this->returnValue($theme));

        $objectManager = Bootstrap::getObjectManager();
        $objectManager->addSharedInstance($viewFileSystem, 'Magento\Framework\View\FileSystem');

        /** @var $moduleReader \Magento\Framework\Module\Dir\Reader */
        $moduleReader = $objectManager->get('Magento\Framework\Module\Dir\Reader');
        $moduleReader->setModuleDir('Magento_Core', 'i18n', dirname(__DIR__) . '/Core/Model/_files/Magento/Core/i18n');
        $moduleReader->setModuleDir(
            'Magento_Catalog',
            'i18n',
            dirname(__DIR__) . '/Core/Model/_files/Magento/Catalog/i18n'
        );

        /** @var \Magento\Core\Model\View\Design $designModel */
        $designModel = $this->getMock(
            'Magento\Core\Model\View\Design',
            array('getDesignTheme'),
            array(
                $objectManager->get('Magento\Framework\StoreManagerInterface'),
                $objectManager->get('Magento\Framework\View\Design\Theme\FlyweightFactory'),
                $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface'),
                $objectManager->get('Magento\Core\Model\ThemeFactory'),
                $objectManager->get('Magento\Framework\ObjectManagerInterface'),
                $objectManager->get('Magento\Framework\App\State'),
                array('frontend' => 'test_default')
            )
        );

        $designModel->expects($this->any())->method('getDesignTheme')->will($this->returnValue($theme));

        $objectManager->addSharedInstance($designModel, 'Magento\Core\Model\View\Design\Proxy');

        $model = $objectManager->create('Magento\Framework\Translate');
        $objectManager->addSharedInstance($model, 'Magento\Framework\Translate');
        $objectManager->removeSharedInstance('Magento\Framework\Phrase\Renderer\Composite');
        $objectManager->removeSharedInstance('Magento\Framework\Phrase\Renderer\Translate');
        \Magento\Framework\Phrase::setRenderer($objectManager->get('Magento\Framework\Phrase\RendererInterface'));
        $model->loadData(\Magento\Framework\App\Area::AREA_FRONTEND);
    }

    /**
     * @dataProvider translateDataProvider
     */
    public function testTranslate($inputText, $expectedTranslation)
    {
        $actualTranslation = __($inputText);
        $this->assertEquals($expectedTranslation, $actualTranslation);
    }

    /**
     * @return array
     */
    public function translateDataProvider()
    {
        return array(
            array('', ''),
            array('Text with different translation on different modules', 'Text translation that was last loaded'),
            array('text_with_no_translation', 'text_with_no_translation'),
            array('Design value to translate', 'Design translated value')
        );
    }
}
