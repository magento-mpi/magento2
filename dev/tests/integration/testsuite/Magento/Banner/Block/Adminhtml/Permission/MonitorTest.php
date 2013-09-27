<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Banner_Block_Adminhtml_Permission_MonitorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @param string $blockType
     * @param string $blockName
     * @param string $tabsType
     * @param string $tabsName
     * @dataProvider prepareLayoutDataProvider
     */
    public function testPrepareLayout($blockType, $blockName, $tabsType, $tabsName)
    {
        $layout = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout');
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config_Scope')
            ->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
        $layout->addBlock($blockType, $blockName);
        $tabs = $layout->addBlock($tabsType, $tabsName);
        $tab = $layout->addBlock(
            'Magento_Banner_Block_Adminhtml_Promo_Catalogrule_Edit_Tab_Banners',
            'banners_section',
            $tabsName
        );
        $tabs->addTab('banners_section', $tab);

        $this->assertContains('banners_section', $tabs->getTabsIds());
        $this->assertTrue($layout->hasElement($blockName));
        $this->assertInstanceOf($blockType, $layout->getBlock($blockName));
        $layout->createBlock('Magento_Banner_Block_Adminhtml_Permission_Monitor', 'bannner.permission.monitor');
        $this->assertFalse($layout->hasElement($blockName));
        $this->assertFalse($layout->getBlock($blockName));
        $this->assertNotContains('banners_section', $tabs->getTabsIds());
    }

    /**
     * @return array
     */
    public function prepareLayoutDataProvider()
    {
        return array(
            array(
                'Magento_Banner_Block_Adminhtml_Promo_Salesrule_Edit_Tab_Banners',
                'salesrule.related.banners',
                'Magento_Adminhtml_Block_Promo_Quote_Edit_Tabs',
                'promo_quote_edit_tabs',
            ),
            array(
                'Magento_Banner_Block_Adminhtml_Promo_Salesrule_Edit_Tab_Banners',
                'catalogrule.related.banners',
                'Magento_Adminhtml_Block_Widget_Tabs',
                'promo_catalog_edit_tabs',
            ),
        );
    }
}
