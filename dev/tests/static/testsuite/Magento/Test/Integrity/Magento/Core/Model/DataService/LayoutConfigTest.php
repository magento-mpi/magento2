<?php
/**
 * Validates that all layouts with service_calls actually reference a valid service call
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Magento\Core\Model\DataService;

class LayoutConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array string[] $_serviceCalls Array of valid service calls available to layouts
     */
    protected static $_serviceCalls;

    /**
     * Gathers all valid service calls from config files
     */
    public static function setUpBeforeClass()
    {
        /**
         * @var array string[] $configFiles
         */
        $configFiles = \Magento\TestFramework\Utility\Files::init()->getConfigFiles('service_calls.xml', array());
        /**
         * @var string $file
         */
        foreach ($configFiles as $file) {
            /**
             * @var \DOMDocument $dom
             */
            $dom = new \DOMDocument();
            $dom->loadXML(file_get_contents($file[0]));

            /**
             * @var \DOMNodeList $serviceCalls
             */
            $serviceCalls = $dom->getElementsByTagName('service_calls');
            $serviceCalls = $serviceCalls->item(0);
            if ($serviceCalls != null && $serviceCalls->hasChildNodes()) {

                /**
                 * @var $serviceCall \DOMNode
                 */
                foreach ($serviceCalls->childNodes as $serviceCall) {
                    if ($serviceCall->localName == 'service_call') {
                        self::$_serviceCalls[] = $serviceCall->attributes->getNamedItem('name')->nodeValue;
                    }
                }
            }
        }
    }

    /**
     * Given a layout file, test whether all of its service calls are valid
     *
     * @param $layoutFile
     *
     * @dataProvider xmlFileDataProvider
     */
    public function testXmlFile($layoutFile)
    {
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($layoutFile));
        $dataList = $dom->getElementsByTagName('data');
        /** @var \DOMNode $data */
        foreach ($dataList as $data) {
            if ($data->hasAttributes()) {
                /** @var \DOMNode $serviceCallAttribute */
                $serviceCallAttribute = $data->attributes->getNamedItem('service_call');
                if ($serviceCallAttribute) {
                    /** @var string $serviceCall */
                    $serviceCall = $serviceCallAttribute->nodeValue;
                    $this->assertContains($serviceCall, self::$_serviceCalls, "Unknown service call: $serviceCall");
                }
            }
        }
    }

    /**
     * @return array
     */
    public function xmlFileDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getLayoutFiles();
    }
}
