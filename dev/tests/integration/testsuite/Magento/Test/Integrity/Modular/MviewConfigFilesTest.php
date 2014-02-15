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

namespace Magento\Test\Integrity\Modular;

class MviewConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configuration acl file list
     *
     * @var array
     */
    protected $fileList = array();

    /**
     * Path to scheme file
     *
     * @var string
     */
    protected $schemeFile;

    protected function setUp()
    {
        $this->schemeFile = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Filesystem')
            ->getPath(\Magento\App\Filesystem::LIB_INTERNAL) . '/Magento/Mview/etc/mview.xsd';
    }

    /**
     * Test each acl configuration file
     * @param string $file
     * @dataProvider mviewConfigFileDataProvider
     */
    public function testIndexerConfigFile($file)
    {
        $domConfig = new \Magento\Config\Dom(file_get_contents($file));
        $result = $domConfig->validate($this->schemeFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "$error\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * @return array
     */
    public function mviewConfigFileDataProvider()
    {
        $fileList = glob(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\Filesystem')
                ->getPath(\Magento\App\Filesystem::APP_DIR) . '/*/*/*/etc/mview.xml'
        );
        $dataProviderResult = array();
        foreach ($fileList as $file) {
            $dataProviderResult[$file] = array($file);
        }
        return $dataProviderResult;
    }
}
