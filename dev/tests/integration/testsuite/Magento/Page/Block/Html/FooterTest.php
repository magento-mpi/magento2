<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Page\Block\Html;

class FooterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Theme
     */
    protected $_theme;

    protected function setUp()
    {
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\View\DesignInterface');
        $this->_theme = $design->setDefaultDesignTheme()->getDesignTheme();
    }

    public function testGetCacheKeyInfo()
    {
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout')
            ->createBlock('Magento\Page\Block\Html\Footer');
        $storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\StoreManagerInterface')->getStore()->getId();
        $this->assertEquals(
            array('PAGE_FOOTER', $storeId, 0, $this->_theme->getId(), null),
            $block->getCacheKeyInfo()
        );
    }
}
