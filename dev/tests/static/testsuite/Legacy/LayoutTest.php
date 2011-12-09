<?php
/**
 * Coverage of deprecated nodes in layout
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Legacy_LayoutTest extends PHPUnit_Framework_TestCase
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
        return Integrity_ClassesTest::viewXmlDataProvider();
    }
}
