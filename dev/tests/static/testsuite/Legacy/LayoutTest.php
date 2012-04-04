<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Coverage of obsolete nodes in layout
 */
class Legacy_LayoutTest extends PHPUnit_Framework_TestCase
{
    protected $_obsoleteNodes = array(
        'PRODUCT_TYPE_simple', 'PRODUCT_TYPE_configurable', 'PRODUCT_TYPE_grouped', 'PRODUCT_TYPE_bundle',
        'PRODUCT_TYPE_virtual', 'PRODUCT_TYPE_downloadable', 'PRODUCT_TYPE_giftcard',
        'catalog_category_default', 'catalog_category_layered', 'catalog_category_layered_nochildren',
        'customer_logged_in', 'customer_logged_out', 'customer_logged_in_psc_handle', 'customer_logged_out_psc_handle',
        'cms_page',
    );

    /**
     * @param string $layoutFile
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
        $this->assertEmpty(
            $layoutXml->xpath('/layout/*[@output="toHtml"]'), 'output="toHtml" is obsolete. Use output="1"'
        );
        foreach ($layoutXml as $handle) {
            $this->assertNotContains($handle->getName(), $this->_obsoleteNodes, 'Layout handle was removed.');
        }
        foreach ($layoutXml->xpath('@helper') as $action) {
            $this->assertNotContains('/', $action->getAtrtibute('helper'));
            $this->assertContains('::', $action->getAtrtibute('helper'));
        }

        if (false !== strpos($layoutFile, 'app/code/core/Mage/Adminhtml/view/adminhtml/sales.xml')) {
            $this->markTestIncomplete("The file {$layoutFile} has to use Mage_Core_Block_Text_List, \n"
                . 'there is no solution to get rid of it right now.'
            );
        }
        $this->assertEmpty($layoutXml->xpath('/layout//block[@type="Mage_Core_Block_Text_List"]'),
            'The class Mage_Core_Block_Text_List is not supposed to be used in layout anymore.'
        );
    }

    /**
     * @return array
     */
    public function layoutFileDataProvider()
    {
        return Utility_Files::init()->getLayoutFiles();
    }
}
