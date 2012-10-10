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

class Mage_Cms_Helper_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * magentoDataFixture Mage/Cms/_files/pages.php
     */
    public function testRenderPage()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + fixture');

        $page = Mage::getSingleton('Mage_Cms_Model_Page');
        $page->load('page_design_modern', 'identifier'); // fixture
        $helper = Mage::helper('Mage_Cms_Helper_Page');
        $result = $helper->renderPage(
            Mage::getModel(
                'Mage_Core_Controller_Front_Action',
                array('request' => new Magento_Test_Request, 'response' => new Magento_Test_Response)
            ),
            $page->getId()
        );
        $this->assertEquals('default/modern/default', Mage::getDesign()->getDesignTheme());
        $this->assertTrue($result);
    }
}
