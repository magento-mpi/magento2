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


namespace Magento\Banner\Block\Adminhtml\Permission;

/**
 * @magentoAppArea adminhtml
 */
class MonitorTest extends \PHPUnit_Framework_TestCase
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
        $layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Config\Scope')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        $layout->addBlock($blockType, $blockName);
        $tabs = $layout->addBlock($tabsType, $tabsName);
        $tab = $layout->addBlock(
            'Magento\Banner\Block\Adminhtml\Promo\Catalogrule\Edit\Tab\Banners',
            'banners_section',
            $tabsName
        );
        $tabs->addTab('banners_section', $tab);

        $this->assertContains('banners_section', $tabs->getTabsIds());
        $this->assertTrue($layout->hasElement($blockName));
        $this->assertInstanceOf($blockType, $layout->getBlock($blockName));
        $layout->createBlock('Magento\Banner\Block\Adminhtml\Permission\Monitor', 'bannner.permission.monitor');
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
                'Magento\Banner\Block\Adminhtml\Promo\Salesrule\Edit\Tab\Banners',
                'salesrule.related.banners',
                'Magento\Adminhtml\Block\Promo\Quote\Edit\Tabs',
                'promo_quote_edit_tabs',
            ),
            array(
                'Magento\Banner\Block\Adminhtml\Promo\Salesrule\Edit\Tab\Banners',
                'catalogrule.related.banners',
                'Magento\Adminhtml\Block\Widget\Tabs',
                'promo_catalog_edit_tabs',
            ),
        );
    }
}
