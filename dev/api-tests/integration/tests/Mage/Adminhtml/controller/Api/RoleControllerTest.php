<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test model admin api role controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Api Team <api-team@magento.com>
 */
class Mage_Adminhtml_Api_RoleControllerTest extends Magento_Test_ControllerTestCaseAbstract
{
    /**
     * Test data filtering on render edit role page
     */
    public function testExistXssOnEdit()
    {
        //generate test item
        /** @var $model Mage_Api_Model_Roles */
        $model = Mage::getModel('api/roles');
        $input = 'testXss <script>alert(1)</script>';
        $model->setName($input)->save();

        /** @var $urlModel Mage_Adminhtml_Model_Url */
        $urlModel = Mage::getSingleton('adminhtml/url');

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
        $helper = Mage::helper('core');
        $expected = $helper->escapeHtml($input);

        $html = $this->getResponse()->getBody();

        if (false === strpos($html, 'testXss')) {
            $this->fail('Edit role page is not rendered.');
        }

        $this->assertTrue((bool) strpos($html, $expected), 'Role name has vulnerability.');
    }
}
