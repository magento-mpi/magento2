<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group integrity
 */
class Integrity_LayoutTest extends Magento_Test_TestCase_IntegrityAbstract
{
    /**
     * @dataProvider layoutFileDataProvider
     */
    public function testLayoutFile($layoutFile)
    {
        $layoutXml = simplexml_load_file($layoutFile);
        $selectorHeadBlock = '(name()="block" or name()="reference") and (@name="head" or @name="convert_root_head")';
        $this->assertEmpty(
            $layoutXml->xpath(
                '//*[' . $selectorHeadBlock . ']/action[@method="addItem"]'
            ),
            "Expected absence of the legacy call(s) to Mage_Page_Block_Html_Head::addItem."
        );
        $this->assertEmpty(
            $layoutXml->xpath(
                '//action[@method="addJs" or @method="addCss"]/parent::*[not(' . $selectorHeadBlock . ')]'
            ),
            "Expected addCss/addJs call(s) within the 'head' block only."
        );
    }

    public function layoutFileDataProvider()
    {
        $codeDir = Mage::getBaseDir('code');
        $designDir = Mage::getBaseDir('design');
        $layoutFiles = array_merge(
            // $designDir/<area>/<package>/<theme>/local.xml
            glob("$designDir/*/*/*/local.xml"),
            // $designDir/<area>/<package>/<theme>/<module>/*.xml
            glob("$designDir/*/*/*/*/*.xml")
        );
        foreach ($this->_getEnabledModules() as $enabledModuleName) {
            list($namespace, $module) = explode('_', $enabledModuleName);
            // $codeDir/<pool>/<namespace>/<module>/view/<area>/*.xml
            $layoutFiles = array_merge($layoutFiles, glob("$codeDir/*/$namespace/$module/view/*/*.xml"));
        }
        $result = array();
        foreach ($layoutFiles as $oneLayoutFile) {
            /* Use filename as a data set name to not include it to every assertion message */
            $result[$oneLayoutFile] = array($oneLayoutFile);
        }
        return $result;
    }
}
