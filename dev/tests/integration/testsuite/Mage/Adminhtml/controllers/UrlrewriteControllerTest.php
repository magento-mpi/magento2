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
    /**
     * Test presence of edit form
     */
    public function testEditActionIsFormPresent()
    {
        $this->dispatch('backend/admin/urlrewrite/edit/id');
        $response = $this->getResponse()->getBody();
        // Check that there is only one instance of edit_form
        $this->assertSelectCount('form#edit_form', 1, $response);
        // Check edit form attributes
        $saveUrl = Mage::helper('Mage_Adminhtml_Helper_Data')->getUrl('*/urlrewrite/save');
        $this->assertTag(array(
            'tag' => 'form',
            'attributes' => array(
                'id' => 'edit_form',
                'method' => 'post',
                'action' => $saveUrl
            )
        ), $response, 'Edit form does not contain all required attributes');
    }
}
