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
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Manage consumers controller
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Adminhtml_OAuth_ConsumerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init titles
     *
     * @return Mage_OAuth_Adminhtml_OAuth_ConsumerController
     */
    public function preDispatch()
    {
        $this->_title($this->__('System'))
                ->_title($this->__('OAuth'))
                ->_title($this->__('Consumers'));
        parent::preDispatch();
        return $this;
    }

    /**
     * Render grid page
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Render grid AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Create page action
     */
    public function newAction()
    {
        /** @var $model Mage_OAuth_Model_Consumer */
        $model = Mage::getModel('oauth/consumer');

        $formData = $this->_getFormData();
        if ($formData) {
            $model->addData($formData);
        } else {
            /** @var $helper Mage_OAuth_Helper_Data */
            $helper = Mage::helper('oauth');
            $model->setKey(
                $helper->generateToken(Mage_OAuth_Model_Consumer::KEY_LENGTH));
            $model->setSecret(
                $helper->generateToken(Mage_OAuth_Model_Consumer::SECRET_LENGTH));
            $this->_setFormData($model->getData());
        }

        Mage::register('current_consumer', $model);

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Edit page action
     */
    public function editAction()
    {
        $id = (int) $this->getRequest()->getParam('id');

        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = Mage::helper('oauth');

        if (!$id) {
            $this->_getSession()->addError(
                $helper->__('Invalid ID parameter.'));
            $this->_redirect('*/*/index');
            return;
        }

        /** @var $model Mage_OAuth_Model_Consumer */
        $model = Mage::getModel('oauth/consumer');
        $model->load($id);

        if (!$model->getId()) {
            $this->_getSession()->addError(
                $helper->__('Entry with ID #%s not found.', $id));
            $this->_redirect('*/*/index');
            return;
        }

        $formData = $this->_getFormData();
        if ($formData) {
            $model->addData($formData);
        }

        Mage::register('current_consumer', $model);


        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Render edit page
     */
    public function saveAction()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$this->_validateFormKey()) {
            if ($id) {
                $this->_redirect('*/*/edit', array('id' => $id));
            } else {
                $this->_redirect('*/*/new', array('id' => $id));
            }
            return;
        }

        $data = $this->getRequest()->getParams();

        //unset unused data
        //skip getting "key" and "secret" because its generated from server side only
        unset($data['id'], $data['back'], $data['form_key'], $data['key'], $data['secret']);

        /** @var $helper Mage_OAuth_Helper_Data */
        $helper = Mage::helper('oauth');

        /** @var $model Mage_OAuth_Model_Consumer */
        $model = Mage::getModel('oauth/consumer');

        if ($id) {
            if (!(int) $id) {
                $this->_getSession()->addError(
                    $helper->__('Invalid ID parameter.'));
                $this->_redirect('*/*/index');
                return;
            }
            $model->load($id);

            if (!$model->getId()) {
                $this->_getSession()->addError(
                    $helper->__('Entry with ID #%s not found.', $id));
                $this->_redirect('*/*/index');
                return;
            }
        } else {
            $dataForm = $this->_getFormData();
            if ($dataForm) {
                $data['key']    = $dataForm['key'];
                $data['secret'] = $dataForm['secret'];
            }
        }

        try {
            $model->addData($data);
            $model->save();
            $this->_getSession()->addSuccess($this->__('The consumer has been saved.'));
            $this->_setFormData(null);
        } catch (Mage_Core_Exception $e) {
            $this->_setFormData($data);
            $this->_getSession()->addError(Mage::helper('core')->escapeHtml($e->getMessage()));
            $this->getRequest()->setParam('back', 'edit');
        } catch (Exception $e) {
            $this->_setFormData(null);
            Mage::logException($e);
            $this->_getSession()->addError('An error occurred on saving consumer data.');
        }

        if ($this->getRequest()->getParam('back')) {
            if ($id || $model->getId()) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
            } else {
                $this->_redirect('*/*/new');
            }
        } else {
            $this->_redirect('*/*/index');
        }
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        /** @var $session Mage_Admin_Model_Session */
        $session = Mage::getSingleton('admin/session');
        return $session->isAllowed('oauth/consumer');
    }

    /**
     * Get form data
     *
     * @return array
     */
    protected function _getFormData()
    {
        return $this->_getSession()->getData('consumer_data', true);
    }

    /**
     * Set form data
     *
     * @param $data
     * @return Mage_OAuth_Adminhtml_OAuth_ConsumerController
     */
    protected function _setFormData($data)
    {
        $this->_getSession()->setData('consumer_data', $data);
        return $this;
    }
}
