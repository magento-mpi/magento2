<?php
namespace Magento\Widget\Model\Config;

/**
 * \Magento\Widget\Model\Config\Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Magento/Backend/controllers/_files/cache/all_types_disabled.php
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\Widget\Model\Config\Reader
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->directoryList = $objectManager->get('Magento\Framework\App\Filesystem\DirectoryList');
        $dirPath = ltrim(
            str_replace($this->directoryList->getRoot(), '', str_replace('\\', '/', __DIR__)) . '/_files',
            '/'
        );
        $this->directoryList->addDirectory(
            \Magento\Framework\App\Filesystem::MODULES_DIR,
            array('path' => $dirPath . '/code')
        );
        $this->directoryList->addDirectory(\Magento\Framework\App\Filesystem::CONFIG_DIR, array('path' => $dirPath));
        $this->directoryList->addDirectory(\Magento\Framework\App\Filesystem::ROOT_DIR, array('path' => $dirPath));

        $filesystem = $objectManager->create(
            'Magento\Framework\App\Filesystem',
            array('directoryList' => $this->directoryList)
        );

        /** @var \Magento\Framework\Module\Declaration\FileResolver $modulesDeclarations */
        $modulesDeclarations = $objectManager->create(
            'Magento\Framework\Module\Declaration\FileResolver',
            array('filesystem' => $filesystem)
        );

        /** @var \Magento\Framework\Module\Declaration\Reader\Filesystem $filesystemReader */
        $filesystemReader = $objectManager->create(
            'Magento\Framework\Module\Declaration\Reader\Filesystem',
            array('fileResolver' => $modulesDeclarations)
        );

        /** @var \Magento\Framework\Module\ModuleList $modulesList */
        $modulesList = $objectManager->create(
            'Magento\Framework\Module\ModuleList',
            array('reader' => $filesystemReader)
        );

        /** @var \Magento\Framework\Module\Dir\Reader $moduleReader */
        $moduleReader = $objectManager->create(
            'Magento\Framework\Module\Dir\Reader',
            array('moduleList' => $modulesList, 'filesystem' => $filesystem)
        );
        $moduleReader->setModuleDir('Magento_Test', 'etc', __DIR__ . '/_files/code/Magento/Test/etc');

        /** @var \Magento\Widget\Model\Config\FileResolver $fileResolver */
        $fileResolver = $objectManager->create(
            'Magento\Widget\Model\Config\FileResolver',
            array('moduleReader' => $moduleReader, 'filesystem' => $filesystem)
        );

        $schema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget.xsd';
        $perFileSchema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget_file.xsd';
        $this->_model = $objectManager->create(
            'Magento\Widget\Model\Config\Reader',
            array(
                'moduleReader' => $moduleReader,
                'fileResolver' => $fileResolver,
                'schema' => $schema,
                'perFileSchema' => $perFileSchema
            )
        );
    }

    public function testRead()
    {
        $result = $this->_model->read('global');
        $expected = include '_files/expectedGlobalArray.php';
        $this->assertEquals($expected, $result);
    }

    public function testReadFile()
    {
        $file = file_get_contents(__DIR__ . '/_files/code/Magento/Test/etc/widget.xml');
        $result = $this->_model->readFile($file);
        $expected = include '_files/expectedGlobalArray.php';
        $this->assertEquals($expected, $result);
    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = array(
            __DIR__ . '/_files/widgetFirst.xml' => file_get_contents(__DIR__ . '/_files/widgetFirst.xml'),
            __DIR__ . '/_files/widgetSecond.xml' => file_get_contents(__DIR__ . '/_files/widgetSecond.xml')
        );
        $fileResolverMock = $this->getMockBuilder(
            'Magento\Framework\Config\FileResolverInterface'
        )->setMethods(
            array('get')
        )->disableOriginalConstructor()->getMock();
        $fileResolverMock->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            $this->equalTo('widget.xml'),
            $this->equalTo('global')
        )->will(
            $this->returnValue($fileList)
        );

        $schema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget.xsd';
        $perFileSchema = __DIR__ . '/../../../../../../../../app/code/Magento/Widget/etc/widget_file.xsd';

        /** @var \Magento\Widget\Model\Config\Reader $model */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Widget\Model\Config\Reader',
            array('fileResolver' => $fileResolverMock, 'schema' => $schema, 'perFileSchema' => $perFileSchema)
        );
        $output = $model->read('global');
        $expected = include '_files/expectedMergedArray.php';
        $this->assertEquals($expected, $output);
    }
}
