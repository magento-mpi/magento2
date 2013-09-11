<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Controller_IndexTest extends Magento_Backend_Utility_Controller
{
    /**
     * @covers \Magento\Adminhtml\Controller\Index::globalSearchAction
     */
    public function testGlobalSearchAction()
    {
        $this->getRequest()->setParam('isAjax', 'true');
        $this->getRequest()->setPost('query', 'dummy');
        $this->dispatch('backend/admin/index/globalSearch');

        $actual = $this->getResponse()->getBody();
        $this->assertEquals(array(), json_decode($actual));
    }
}
