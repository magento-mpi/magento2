<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Modular;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class IndexerConfigFilesTest extends \PHPUnit_Framework_TestCase
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
        /** @var Filesystem $filesystem */
        $filesystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\Filesystem'
        );
        $this->schemeFile = $filesystem->getDirectoryRead(DirectoryList::APP)
            ->getAbsolutePath('code/Magento/Indexer/etc/indexer.xsd');
    }

    /**
     * Test each acl configuration file
     * @param string $file
     * @dataProvider indexerConfigFileDataProvider
     */
    public function testIndexerConfigFile($file)
    {
        $domConfig = new \Magento\Framework\Config\Dom(file_get_contents($file));
        $result = $domConfig->validate($this->schemeFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error}\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * @return array
     */
    public function indexerConfigFileDataProvider()
    {
        /** @var Filesystem $filesystem */
        $filesystem = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\Filesystem'
        );
        $fileList = glob(
            $filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath() . '/*/*/*/etc/indexer.xml'
        );
        $dataProviderResult = array();
        foreach ($fileList as $file) {
            $dataProviderResult[$file] = array($file);
        }
        return $dataProviderResult;
    }
}
