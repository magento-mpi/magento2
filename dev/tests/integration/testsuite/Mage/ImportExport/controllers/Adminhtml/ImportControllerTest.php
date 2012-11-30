<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_ImportExport_Adminhtml_ImportControllerTest extends Mage_Adminhtml_Utility_Controller
{
    public function testGetFilterAction()
    {
        $this->dispatch('backend/admin/import/index');
        $body = $this->getResponse()->getBody();
        $this->assertContains(Mage::helper('Mage_ImportExport_Helper_Data')->getMaxUploadSizeMessage(), $body);
    }
}
