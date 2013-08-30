<?php
/**
 * Test VS backwards-incompatible changes in widget.xml
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * A test for backwards-incompatible change in widget.xml structure
 */
class Legacy_Magento_Widget_XmlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $file
     * @dataProvider widgetXmlFilesDataProvider
     */
    public function testClassFactoryNames($file)
    {
        $xml = simplexml_load_file($file);
        $nodes = $xml->xpath('/widgets/*[@type]') ?: array();
        /** @var SimpleXMLElement $node */
        foreach ($nodes as $node) {
            $type = (string)$node['type'];
            $this->assertNotRegExp('/\//', $type, "Factory name detected: {$type}.");
        }
    }

    /**
     * @param string $file
     * @dataProvider widgetXmlFilesDataProvider
     */
    public function testBlocksIntoContainers($file)
    {
        $xml = simplexml_load_file($file);
        $this->assertSame(array(), $xml->xpath('/widgets/*/supported_blocks'),
            'Obsolete node: <supported_blocks>. To be replaced with <supported_containers>'
        );
        $this->assertSame(array(), $xml->xpath('/widgets/*/*/*/block_name'),
            'Obsolete node: <block_name>. To be replaced with <container_name>'
        );
    }

    /**
     * @return array
     */
    public function widgetXmlFilesDataProvider()
    {
        return Utility_Files::init()->getConfigFiles('widget.xml');
    }
}
