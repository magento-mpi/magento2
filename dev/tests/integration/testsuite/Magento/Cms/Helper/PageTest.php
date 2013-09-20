<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Helper;

class PageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Cms/_files/pages.php
     */
    public function testRenderPage()
    {
        $arguments = array(
            'request' => new \Magento\TestFramework\Request(),
            'response' => \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                ->get('Magento\TestFramework\Response')
        );
        $context = \Mage::getModel('Magento\Core\Controller\Varien\Action\Context', $arguments);
        $page = \Mage::getSingleton('Magento\Cms\Model\Page');
        $page->load('page_design_blank', 'identifier'); // fixture
        /** @var $pageHelper \Magento\Cms\Helper\Page */
        $pageHelper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Cms\Helper\Page');
        $result = $pageHelper->renderPage(
            \Mage::getModel('Magento\Core\Controller\Front\Action', array('context' => $context)),
            $page->getId()
        );
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\View\DesignInterface');
        $this->assertEquals('magento_blank', $design->getDesignTheme()->getThemePath());
        $this->assertTrue($result);
    }
}
