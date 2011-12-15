<?php
/**
 * Coverage of obsolete nodes in layout
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
        $suggestion = sprintf(Legacy_ObsoleteCodeTest::SUGGESTION_MESSAGE, 'addCss/addJss');
        $layoutXml = simplexml_load_file($layoutFile);
        $selectorHeadBlock = '(name()="block" or name()="reference") and (@name="head" or @name="convert_root_head")';
        $this->assertEmpty(
            $layoutXml->xpath(
                '//*[' . $selectorHeadBlock . ']/action[@method="addItem"]'
            ),
            "Mage_Page_Block_Html_Head::addItem is obsolete. $suggestion"
        );
        $this->assertEmpty(
            $layoutXml->xpath(
                '//action[@method="addJs" or @method="addCss"]/parent::*[not(' . $selectorHeadBlock . ')]'
            ),
            "Calls addCss/addJs are allowed within the 'head' block only. Verify integrity of the nodes nesting."
        );
    }

    public function layoutFileDataProvider()
    {
        return FileDataProvider::getLayoutFiles();
    }
}
