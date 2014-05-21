<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Model\Installer;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_tmpDir = '';

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected static $_varDirectory;

    public static function setUpBeforeClass()
    {
        /** @var \Magento\Framework\App\Filesystem $filesystem */
        $filesystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Framework\App\Filesystem');
        self::$_varDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::VAR_DIR);
        self::$_tmpDir = self::$_varDirectory->getAbsolutePath('ConfigTest');
        self::$_varDirectory->create(self::$_varDirectory->getRelativePath(self::$_tmpDir));
    }

    public static function tearDownAfterClass()
    {
        self::$_varDirectory->delete(self::$_varDirectory->getRelativePath(self::$_tmpDir));
    }

    public function testInstall()
    {
        file_put_contents(self::$_tmpDir . '/local.xml.template', "test; {{date}}; {{base_url}}; {{unknown}}");
        $expectedFile = self::$_tmpDir . '/local.xml';

        $request = $this->getMock('Magento\Framework\App\Request\Http', array('getDistroBaseUrl'), array(), '', false);

        $request->expects($this->once())->method('getDistroBaseUrl')->will($this->returnValue('http://example.com/'));
        $expectedContents = "test; <![CDATA[d-d-d-d-d]]>; <![CDATA[http://example.com/]]>; {{unknown}}";

        $this->assertFileNotExists($expectedFile);

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $directoryList = $objectManager->create(
            'Magento\Framework\App\Filesystem\DirectoryList',
            array(
                'root' => self::$_tmpDir,
                'directories' => array(\Magento\Framework\App\Filesystem::CONFIG_DIR => array('path' => self::$_tmpDir))
            )
        );
        $objectManager->get('\Magento\Framework\App\Filesystem\DirectoryList\Configuration')->configure($directoryList);
        $filesystem = $objectManager->create(
            'Magento\Framework\App\Filesystem',
            array('directoryList' => $directoryList)
        );
        $model = $objectManager->create(
            'Magento\Install\Model\Installer\Config',
            array('request' => $request, 'filesystem' => $filesystem)
        );

        $model->install();
        $this->assertFileExists($expectedFile);
        $this->assertStringEqualsFile($expectedFile, $expectedContents);
    }

    public function testGetFormData()
    {
        /** @var $model \Magento\Install\Model\Installer\Config */
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Install\Model\Installer\Config'
        );
        /** @var $result \Magento\Framework\Object */
        $result = $model->getFormData();
        $this->assertInstanceOf('Magento\Framework\Object', $result);
        $data = $result->getData();
        $this->assertArrayHasKey('db_host', $data);
    }
}
