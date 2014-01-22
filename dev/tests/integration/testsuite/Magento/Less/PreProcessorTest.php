<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Less;

class PreProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Css\PreProcessor\Less
     */
    protected $model;

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\Filesystem::PARAM_APP_DIRS => array(
                \Magento\Filesystem::PUB_LIB => array('path' => __DIR__ . '/_files/lib'),
                \Magento\App\Filesystem::THEMES_DIR => array('path' => __DIR__ . '/_files/design')
            )
        ));
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->objectManager->get('Magento\App\State')->setAreaCode('frontend');
        $this->model = $this->objectManager->create('Magento\Css\PreProcessor\Less');
    }

    /**
     * @magentoDataFixture Magento/Less/_files/themes.php
     * @magentoAppIsolation enabled
     * @magentoAppArea frontend
     */
    public function testProcess()
    {
        /** @var $filesystem \Magento\Filesystem */
        $filesystem = $this->objectManager->get('Magento\Filesystem');
        $targetDirectory = $filesystem->getDirectoryWrite(\Magento\Filesystem::TMP);
        $designParams = array('area' => 'frontend', 'theme' => 'test_pre_process');
        $cssTargetFile = $this->model->process('source.css', $designParams, $targetDirectory);
        /** @var $viewFilesystem \Magento\View\FileSystem */
        $viewFilesystem = $this->objectManager->get('Magento\View\FileSystem');
        $this->assertFileEquals($viewFilesystem->getViewFile('source.css', $designParams), $cssTargetFile);
    }
}
