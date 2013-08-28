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

class Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_CustomerTest
    extends Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_TagTestCaseAbstract
{
    /**
     * @var string
     */
    protected $_modelName = 'Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer';

    /**
     * @var string
     */
    protected $_title = 'Customers Tagged Product';

    /**
     * @covers Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer::getTabLabel
     * @covers Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer::getTabTitle
     * @covers Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer::canShowTab
     * @covers Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer::isHidden
     * @covers Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer::getTabClass
     * @covers Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer::getAfter
     * @dataProvider methodListDataProvider
     * @param string $method
     */
    public function testDefinedPublicMethods($method)
    {
        $this->$method();
    }
}
