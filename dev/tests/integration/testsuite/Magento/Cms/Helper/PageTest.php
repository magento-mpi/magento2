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
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $arguments = array(
            'request' => $objectManager->get('Magento\TestFramework\Request'),
            'response' => $objectManager->get('Magento\TestFramework\Response')
        );
        $context = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Controller\Varien\Action\Context', $arguments);
        $page = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Cms\Model\Page');
        $page->load('page_design_blank', 'identifier'); // fixture
        /** @var $pageHelper \Magento\Cms\Helper\Page */
        $pageHelper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Cms\Helper\Page');
        $result = $pageHelper->renderPage(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Controller\Front\Action', array('context' => $context)),
            $page->getId()
        );
        $design = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\View\DesignInterface');
        $this->assertEquals('magento_blank', $design->getDesignTheme()->getThemePath());
        $this->assertTrue($result);
    }
}
