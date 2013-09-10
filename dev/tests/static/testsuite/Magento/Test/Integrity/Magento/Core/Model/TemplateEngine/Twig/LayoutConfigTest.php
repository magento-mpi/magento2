<?php
/**
 * Validates that all layouts with service_calls actually reference a valid service call
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Magento_Core_Model_TemplateEngine_Twig_LayoutConfigTest extends PHPUnit_Framework_TestCase
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
        $configFiles = Magento_TestFramework_Utility_Files::init()->getConfigFiles('service_calls.xml', array());
        /**
         * @var string $file
         */
        foreach ($configFiles as $file) {
            /**
             * @var DOMDocument $dom
             */
            $dom = new DOMDocument();
            $dom->loadXML(file_get_contents($file[0]));

            /**
             * @var DOMNodeList $serviceCalls
             */
            $serviceCalls = $dom->getElementsByTagName('service_calls');
            $serviceCalls = $serviceCalls->item(0);
            if ($serviceCalls != null && $serviceCalls->hasChildNodes()) {

                /**
                 * @var $serviceCall DOMNode
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
     * Given a layout file, test whetehr all of its service calls are valid
     *
     * @param $layoutFile
     * @param bool $dummy Describes whether a dummy array was passed in, indicating that no files were found
     *
     * @dataProvider xmlDataProvider
     */
    public function testXmlFile($layoutFile, $dummy = false)
    {
        if (!$dummy) {
            /**
             * @var DOMDocument $dom
             */
            $dom = new DOMDocument();
            $dom->loadXML(file_get_contents($layoutFile));
            $this->assertNotNull($dom);

            /**
             * @var DOMNodeList $dataList
             */
            $dataList = $dom->getElementsByTagName('data');

            /**
             * @var DOMNode $data
             */
            foreach ($dataList as $data) {
                if ($data->hasAttributes()) {
                    /** @var DOMNode $serviceCallAttribute */
                    $serviceCallAttribute = $data->attributes->getNamedItem('service_call');
                    if ($serviceCallAttribute) {

                        /**
                         * @var string $serviceCall
                         */
                        $serviceCall = $serviceCallAttribute->nodeValue;
                        $this->assertTrue(
                            in_array($serviceCall, self::$_serviceCalls),
                            "Unknown service call: $serviceCall"
                        );
                    }
                }
            }
        }
    }

    /**
     * Provides a list of layout files to test, or a dummy array if no files are found
     *
     * @return array
     */
    public function xmlDataProvider()
    {
        $files = Magento_TestFramework_Utility_Files::init()->getLayoutFiles();
        if (empty($files)) {
            $files = array(
                array('dummy', true)
            );
        }

        return $files;
    }
}
