<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for validation check of the giftregistry.xml and xsd for this file
 *
 * Class \Magento\Test\Integrity\Modular\GiftRegistryConfigFileTest
 */
namespace Magento\Test\Integrity\Modular;

class GiftRegistryConfigFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Schema for gift registry
     *
     * @var string
     */
    protected $_schemaFile;

    /**
     * Set up schema file
     */
    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_schemaFile = $objectManager->get('Magento\GiftRegistry\Model\Config\SchemaLocator')->getSchema();
    }

    /**
     * Validation test for xml and xsd files
     * Can fail if the libXML errors exist
     *
     * @param string $file
     * @dataProvider giftRegistryConfigFilesDataProvider
     */
    public function testGiftRegistryConfigValidation($file)
    {
        $errors = array();
        $dom = new \Magento\Config\Dom(file_get_contents($file)) ;
        $result = $dom->validate($this->_schemaFile, $errors);
        $message = "Invalid XML-file: {$file}\n";
        foreach ($errors as $error) {
            $message .= "{$error->message} Line: {$error->line}\n";
        }
        $this->assertTrue($result, $message);
    }

    /**
     * Data provider for testGiftRegistryConfigValidation
     *
     * @return array
     */
    public function giftRegistryConfigFilesDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getConfigFiles('giftregistry.xml');
    }
}
