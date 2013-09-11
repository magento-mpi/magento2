<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_ImportExport_Controller_Adminhtml_ImportTest extends Magento_Backend_Utility_Controller
{
    public function testGetFilterAction()
    {
        $this->dispatch('backend/admin/import/index');
        $body = $this->getResponse()->getBody();
        $this->assertContains((string)\Mage::helper('Magento\ImportExport\Helper\Data')->getMaxUploadSizeMessage(),
            $body);
    }
}
