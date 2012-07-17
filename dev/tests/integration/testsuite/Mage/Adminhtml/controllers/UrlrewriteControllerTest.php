<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_UrlrewriteControllerTest extends Mage_Adminhtml_Utility_Controller
{
    public function testEditActionIsFormPresent()
    {
        $this->dispatch('backend/admin/urlrewrite/edit/id');
        $saveUrl = Mage::helper('Mage_Adminhtml_Helper_Data')->getUrl('*/urlrewrite/save');
        $this->assertContains(
            '<form id="edit_form" action="' . $saveUrl . '" method="post">',
            $this->getResponse()->getBody()
        );
    }
}
