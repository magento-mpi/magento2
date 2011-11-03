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
 * @category
 * @package     _home
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * TheFind feed attribute map grid controller
 *
 * @category    Find
 * @package     Find_Feed
 */
class Find_Feed_Adminhtml_Codes_GridController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Point active menu item and set title
     *
     * @return Find_Feed_Adminhtml_Codes_GridController
     */
    protected function _init()
    {
        $this->_setActiveMenu('catalog/feed');
        $this
            ->_title($this->__('Catalog'))
            ->_title($this->__('TheFind'));

        return $this;
    }

    /**
     * Main index action
     *
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_init()->_title($this->__('Attributes'), false);
        $this->renderLayout();
    }

    /**
     * Grid action
     *
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Find_Feed_Block_Adminhtml_List_Codes_Grid')->toHtml()
        );
    }

    /**
     * Grid edit form action
     *
     */
    public function editFormAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Find_Feed_Block_Adminhtml_Edit_Codes')->toHtml()
        );
    }

    /**
     * Save grid edit form action
     *
     */
    public function saveFormAction()
    {
        $codeId = $this->getRequest()->getParam('code_id');
        $response = new Varien_Object();
        try {
            $model  = Mage::getModel('Find_Feed_Model_Codes');
            if ($codeId) {
                $model->load($codeId);
            }
            $model->setImportCode($this->getRequest()->getParam('import_code'));
            $model->setEavCode($this->getRequest()->getParam('eav_code'));
            $model->setIsImported(intval($this->getRequest()->getParam('is_imported')));
            $model->save();
            $response->setError(0);
        } catch(Exception $e) {
            $response->setError(1);
            $response->setMessage('Save error');
        }
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Codes (attribute map) list for mass action
     *
     * @return array
     */
    protected function _getMassActionCodes()
    {
        $idList = $this->getRequest()->getParam('code_id');
        if (!empty($idList)) {
            $codes = array();
            foreach ($idList as $id) {
                $model = Mage::getModel('Find_Feed_Model_Codes');
                if ($model->load($id)) {
                    array_push($codes, $model);
                }
            }
            return $codes;
        } else {
            return array();
        }
    }

    /**
     * Set imported codes (attribute map) mass action
     */
    public function massEnableAction()
    {
        $updatedCodes = 0;
        foreach ($this->_getMassActionCodes() as $code) {
            $code->setIsImported(1);
            $code->save();
            $updatedCodes++;
        }
        if ($updatedCodes > 0) {
            $this->_getSession()->addSuccess(Mage::helper('Find_Feed_Helper_Data')->__("%s codes imported", $updatedCodes));
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Set not imported codes (attribute map) mass action
     */
    public function massDisableAction()
    {
        $updatedCodes = 0;
        foreach ($this->_getMassActionCodes() as $code) {
            $code->setIsImported(0);
            $code->save();
            $updatedCodes++;
        }
        if ($updatedCodes > 0) {
            $this->_getSession()->addSuccess(Mage::helper('Find_Feed_Helper_Data')->__("%s codes not imported", $updatedCodes));
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Delete codes (attribute map) mass action
     */
    public function deleteAction()
    {
        $updatedCodes = 0;
        foreach ($this->_getMassActionCodes() as $code) {
            $code->delete();
            $updatedCodes++;
        }
        if ($updatedCodes > 0) {
            $this->_getSession()->addSuccess(Mage::helper('Find_Feed_Helper_Data')->__("%s codes deleted", $updatedCodes));
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed() {
        return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('catalog/feed/import_products');
    }
}
