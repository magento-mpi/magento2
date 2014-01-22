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

class NewIndexerConfigFilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Configuration acl file list
     *
     * @var array
     */
    protected $_fileList = array();

    /**
     * Path to scheme file
     *
     * @var string
     */
    protected $_schemeFile;

    protected function setUp()
    {
        $this->_schemeFile = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Filesystem')
            ->getPath(\Magento\Filesystem::APP) . '/code/Magento/Indexer/etc/indexer.xsd';
    }

    /**
     * Test each acl configuration file
     * @param string $file
     * @dataProvider indexerConfigFileDataProvider
     */
    public function testIndexerConfigFile($file)
    {
        $this->markTestIncomplete('Will enable after first indexer will be implemented');
        $domConfig = new \Magento\Config\Dom(file_get_contents($file));
        $result = $domConfig->validate($this->_schemeFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "$error\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * @return array
     */
    public function indexerConfigFileDataProvider()
    {
        $fileList = glob(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Filesystem')
                ->getPath(\Magento\Filesystem::APP) . '/*/*/*/etc/indexer.xml'
        );
        $dataProviderResult = array();
        foreach ($fileList as $file) {
            $dataProviderResult[$file] = array($file);
        }
        return $dataProviderResult;
    }
}
