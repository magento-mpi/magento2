<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Install_Model_Installer_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_tmpDir = '';

    public static function setUpBeforeClass()
    {
        self::$_tmpDir = Mage::getBaseDir(\Magento\Core\Model\Dir::VAR_DIR) . DIRECTORY_SEPARATOR . __CLASS__;
        mkdir(self::$_tmpDir);
    }

    public static function tearDownAfterClass()
    {
        \Magento\Io\File::rmdirRecursive(self::$_tmpDir);
    }

    public function testInstall()
    {
        file_put_contents(self::$_tmpDir . '/local.xml.template', "test; {{date}}; {{base_url}}; {{unknown}}");
        $expectedFile = self::$_tmpDir . '/local.xml';

        $request = $this->getMock(
            '\Magento\Core\Controller\Request\Http',
            array('getDistroBaseUrl'),
            array(),
            '',
            false
        );

        $request->expects($this->once())->method('getDistroBaseUrl')->will($this->returnValue('http://example.com/'));
        $expectedContents = "test; <![CDATA[d-d-d-d-d]]>; <![CDATA[http://example.com/]]>; {{unknown}}";
        $dirs = new \Magento\Core\Model\Dir(
            self::$_tmpDir,
            array(),
            array(\Magento\Core\Model\Dir::CONFIG => self::$_tmpDir)
        );

        $this->assertFileNotExists($expectedFile);
        $filesystem = new \Magento\Filesystem(new \Magento\Filesystem\Adapter\Local);
        $model = Mage::getModel('\Magento\Install\Model\Installer\Config', array(
            'request' => $request, 'dirs' => $dirs, 'filesystem' => $filesystem
        ));
        $model->install();
        $this->assertFileExists($expectedFile);
        $this->assertStringEqualsFile($expectedFile, $expectedContents);
    }

    public function testGetFormData()
    {
        /** @var $model \Magento\Install\Model\Installer\Config */
        $model = Mage::getModel('\Magento\Install\Model\Installer\Config');
        /** @var $result \Magento\Object */
        $result = $model->getFormData();
        $this->assertInstanceOf('\Magento\Object', $result);
        $data = $result->getData();
        $this->assertArrayHasKey('db_host', $data);
    }
}
