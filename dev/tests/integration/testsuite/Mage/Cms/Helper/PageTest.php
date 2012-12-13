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
     * @magentoDataFixture Mage/Cms/_files/pages.php
     */
    public function testRenderPage()
    {
        $page = Mage::getSingleton('Mage_Cms_Model_Page');
        $page->load('page_design_modern', 'identifier'); // fixture
        /** @var $helper Mage_Cms_Helper_Page */
        $helper = Mage::helper('Mage_Cms_Helper_Page');
        $result = $helper->renderPage(
            Mage::getModel(
                'Mage_Core_Controller_Front_Action',
                array(
                    new Magento_Test_Request(),
                    new Magento_Test_Response(),
                    'frontend',
                    Mage::getObjectManager(),
                    Mage::getObjectManager()->get('Mage_Core_Controller_Varien_Front'),
                    Mage::getObjectManager()->get('Mage_Core_Model_Layout_Factory')
                )
            ),
            $page->getId()
        );
        $this->assertEquals('default/modern', Mage::getDesign()->getDesignTheme()->getThemePath());
        $this->assertTrue($result);
    }
}
