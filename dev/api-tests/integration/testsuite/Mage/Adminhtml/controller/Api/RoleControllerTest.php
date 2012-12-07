<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test model admin api role controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Api Team <api-team@magento.com>
 */
class Mage_Adminhtml_Api_RoleControllerTest extends Magento_Test_TestCase_ControllerAbstract
{
    /**
     * Test data filtering on render edit role page
     */
    public function testExistXssOnEdit()
    {
        //generate test item
        /** @var $model Mage_Api_Model_Roles */
        $model = Mage::getModel('Mage_Api_Model_Roles');
        $input = 'testXss <script>alert(1)</script>';
        $model->setName($input)->save();

        /** @var $urlModel Mage_Adminhtml_Model_Url */
        $urlModel = Mage::getSingleton('Mage_Adminhtml_Model_Url');

        try {
            //testing
            $this->getRequest()->setParams(array(
                'rid' => $model->getId(),
                'key' => $urlModel->getSecretKey()
            ));
            $this->loginToAdmin();
            $this->dispatch('admin/api_role/editRole');
        } catch (Exception $e) {
            //remove added item
            $model->delete();
            throw $e;
        }

        //remove added item
        $model->delete();

        /** @var $helper Mage_Core_Helper_Data */
        $helper = Mage::helper('Mage_Core_Helper_Data');
        $expected = $helper->escapeHtml($input);

        $html = $this->getResponse()->getBody();

        if (false === strpos($html, 'testXss')) {
            $this->fail('Edit role page is not rendered.');
        }

        $this->assertTrue((bool) strpos($html, $expected), 'Role name has vulnerability.');
    }
}
