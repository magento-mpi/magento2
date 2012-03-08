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

class Legacy_Mage_Widget_XmlTest extends PHPUnit_Framework_TestCase
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
     * @return array
     */
    public function widgetXmlFilesDataProvider()
    {
        return Util_Files::getConfigFiles('widget.xml');
    }
}
