<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Test\Legacy\Magento\Framework\Module;

/**
 * Test for obsolete nodes/attributes in the module.xml
 */
class ModuleXMLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider moduleXmlDataProvider
     */
    public function testModuleXml($file)
    {
        $xml = simplexml_load_file($file);
        $this->assertEmpty(
            $xml->xpath('/config/module/@version'),
            'The "version" attribute is obsolete. Use "schema_version" instead.'
        );
        $this->assertEmpty(
            $xml->xpath('/config/module/@active'),
            'The "active" attribute is obsolete. The list of active modules is defined in deployment configuration.'
        );
    }

    /**
     * @return array
     */
    public function moduleXmlDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getConfigFiles('module.xml');
    }
}
