<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category   Enterprise
 * @package    Enterprise_Banner
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Banner_Adminhtml_BannerController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Render Banner grid
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('enterprise_banner/adminhtml_banner_grid', 'banner.grid')
                ->toHtml()
        );
    }

    /**
     * Banners list
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('cms/enterprise_banner');
        $this->renderLayout();
    }

    /**
     * Create new banner
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit action
     *
     */
    public function editAction()
    {

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $this->_initBanner('id');
        $model = Mage::registry('current_banner');

        if (!$model->getId() && $id) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('enterprise_banner')->__('This Banner no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->loadLayout()
            ->_addBreadcrumb($id ? Mage::helper('enterprise_banner')->__('Edit Banner') : Mage::helper('enterprise_banner')->__('New Banner'),
                             $id ? Mage::helper('enterprise_banner')->__('Edit Banner') : Mage::helper('enterprise_banner')->__('New Banner'))
            ->renderLayout();
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        $redirectBack = $this->getRequest()->getParam('back', false);
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            $data = $this->_filterPostData($data);
            // init model and set data
            $model = Mage::getModel('enterprise_banner/banner');
            if (!empty($data)) {
                $model->addData($data);
            }

            // try to save it
            try {
                // save the data
                $model->save();

                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('enterprise_banner')->__('Error while saving banner data. Please review log and try again.'));
                Mage::logException($e);
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
        }
        if ($redirectBack) {
            $this->_redirect('*/*/edit', array('_current' => true));
        }
        else {
            $this->_redirect('*/*/');
        }
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('enterprise_banner/banner');
                $model->load($id);
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('enterprise_banner')->__('Banner was successfully deleted'));
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('enterprise_banner')->__('Error while deletion banner data. Please review log and try again.'));
                Mage::logException($e);
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('enterprise_banner')->__('Unable to find a Banner to delete'));
        // go to grid
        $this->_redirect('*/*/');
    }

    /**
     * Load Banenr from request
     *
     * @param string $idFieldName
     */
    protected function _initBanner($idFieldName = 'banner_id')
    {
        $id = (int)$this->getRequest()->getParam($idFieldName);
        $model = Mage::getModel('enterprise_banner/banner');
        if ($id) {
            $model->load($id);
        }
        Mage::register('current_banner', $model);
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
                'date_format' => Mage::app()->getLocale()->getDateFormat()
            ));

        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
                'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
            ));

        if (isset($data['date_expires']) && $data['date_expires']) {
            $data['date_expires'] = $filterInput->filter($data['date_expires']);
            $data['date_expires'] = $filterInternal->filter($data['date_expires']);
        }

        return $data;
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/enterprise_banner');
    }

    public function salesRuleGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function catalogRuleGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
