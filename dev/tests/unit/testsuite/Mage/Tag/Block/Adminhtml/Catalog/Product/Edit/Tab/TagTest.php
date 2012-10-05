<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_TagTest
    extends Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_TagTestCaseAbstract
{
    /**
     * @var string
     */
    protected $_modelName = 'Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag';

    /**
     * @var string
     */
    protected $_title = 'Product Tags';

    /**
     * @covers Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag::getTabLabel
     * @covers Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag::getTabTitle
     * @covers Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag::canShowTab
     * @covers Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag::isHidden
     * @covers Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag::getTabClass
     * @covers Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag::getAfter
     *
     * @dataProvider methodListDataProvider
     * @param string $method
     */
    public function testDefinedPublicMethods($method)
    {
        $this->$method();
    }
}
