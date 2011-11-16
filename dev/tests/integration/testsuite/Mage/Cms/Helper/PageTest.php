<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Cms
 */
class Mage_Cms_Helper_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Cms/_files/pages.php
     */
    public function testRenderPage()
    {
        $page = Mage::getSingleton('Mage_Cms_Model_Page');
        $page->load('page_design_modern', 'identifier'); // fixture
        $helper = new Mage_Cms_Helper_Page;
        $result = $helper->renderPage(
            new Mage_Core_Controller_Front_Action(new Magento_Test_Request, new Magento_Test_Response), $page->getId()
        );
        $this->assertEquals('default/modern/default', Mage::getDesign()->getDesignTheme());
        $this->assertTrue($result);
    }
}
