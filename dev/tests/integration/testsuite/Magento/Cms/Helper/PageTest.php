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

class Magento_Cms_Helper_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Cms/_files/pages.php
     */
    public function testRenderPage()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $arguments = array(
            'request' => $objectManager->get('Magento_TestFramework_Request'),
            'response' => $objectManager->get('Magento_TestFramework_Response')
        );
        $context = Mage::getModel('Magento\Core\Controller\Varien\Action\Context', $arguments);
        $page = Mage::getSingleton('Magento\Cms\Model\Page');
        $page->load('page_design_blank', 'identifier'); // fixture
        /** @var $pageHelper \Magento\Cms\Helper\Page */
        $pageHelper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Cms\Helper\Page');
        $result = $pageHelper->renderPage(
            Mage::getModel('Magento\Core\Controller\Front\Action', array('context' => $context)),
            $page->getId()
        );
        $design = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\View\DesignInterface');
        $this->assertEquals('magento_blank', $design->getDesignTheme()->getThemePath());
        $this->assertTrue($result);
    }
}
