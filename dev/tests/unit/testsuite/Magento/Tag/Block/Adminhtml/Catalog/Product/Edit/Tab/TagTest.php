<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_TagTest
    extends Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_TagTestCaseAbstract
{
    /**
     * @var string
     */
    protected $_modelName = 'Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag';

    /**
     * @var string
     */
    protected $_title = 'Product Tags';

    /**
     * @covers Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag::getTabLabel
     * @covers Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag::getTabTitle
     * @covers Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag::canShowTab
     * @covers Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag::isHidden
     * @covers Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag::getTabClass
     * @covers Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag::getAfter
     *
     * @dataProvider methodListDataProvider
     * @param string $method
     */
    public function testDefinedPublicMethods($method)
    {
        $this->$method();
    }
}
