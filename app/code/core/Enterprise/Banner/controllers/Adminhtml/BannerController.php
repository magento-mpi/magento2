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

        $this->loadLayout();
        $this->_setActiveMenu('cms/enterprise_banner');
        $this->_addBreadcrumb($id ? Mage::helper('enterprise_banner')->__('Edit Banner') : Mage::helper('enterprise_banner')->__('New Banner'),
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
            if (isset($data['banner_catalog_rules'])) {
                $related = Mage::helper('adminhtml/js')->decodeInput($data['banner_catalog_rules']);
                foreach ($related as $_key => $_rid) {
                    $related[$_key] = (int)$_rid;
                }
                $data['banner_catalog_rules'] = $related;
            }
            if (isset($data['banner_sales_rules'])) {
                $related = Mage::helper('adminhtml/js')->decodeInput($data['banner_sales_rules']);
                foreach ($related as $_key => $_rid) {
                    $related[$_key] = (int)$_rid;
                }
                $data['banner_sales_rules'] = $related;
            }
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
     *
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
                $this->_getSession()->addError(Mage::helper('enterprise_banner')->__('Error while deleting banner data. Please review log and try again.'));
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
     * Delete specified banners using grid massaction
     *
     */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('banner');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select banner(s)'));
        }
        else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getSingleton('enterprise_banner/banner')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully deleted', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('enterprise_banner')->__('Error while mass delete banners. Please review log and try again.'));
                Mage::logException($e);
                return;
            }
        }
        $this->_redirect('*/*/index');
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
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/enterprise_banner');
    }

    /**
     * Render Banner grid
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Banner sales rule grid action on promotions tab
     * Load banner by ID from post data
     * Register banner model
     *
     */
    public function salesRuleGridAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->_initBanner('id');
        $model = Mage::registry('current_banner');

        if (!$model->getId() && $id) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('enterprise_banner')->__('This Banner no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        $this->loadLayout();
        $this->getLayout()
            ->getBlock('banner_salesrule_grid')
            ->setSelectedSalesRules($this->getRequest()->getPost('selected_salesrules'));
        $this->renderLayout();
    }

    /**
     * Banner catalog rule grid action on promotions tab
     * Load banner by ID from post data
     * Register banner model
     *
     */
    public function catalogRuleGridAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->_initBanner('id');
        $model = Mage::registry('current_banner');

        if (!$model->getId() && $id) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('enterprise_banner')->__('This Banner no longer exists'));
            $this->_redirect('*/*/');
            return;
        }

        $this->loadLayout();
        $this->getLayout()
            ->getBlock('banner_catalogrule_grid')
            ->setSelectedCatalogRules($this->getRequest()->getPost('selected_catalogrules'));
        $this->renderLayout();
    }

    /**
     * Banner binding tab grid action on sales rule
     *
     */
    public function salesRuleBannersGridAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('salesrule/rule');

        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('salesrule')->__('This rule no longer exists'));
                $this->_redirect('*/*');
                return;
            }
        }
        Mage::register('current_promo_quote_rule', $model);
        $this->loadLayout();
        $this->getLayout()
            ->getBlock('related_salesrule_banners_grid')
            ->setSelectedSalesruleBanners($this->getRequest()->getPost('selected_salesrule_banners'));
        $this->renderLayout();
    }

   /**
     * Banner binding tab grid action on catalog rule
     *
     */
    public function catalogRuleBannersGridAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('catalogrule/rule');

        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalogrule')->__('This rule no longer exists'));
                $this->_redirect('*/*');
                return;
            }
        }
        Mage::register('current_promo_catalog_rule', $model);
        $this->loadLayout();
        $this->getLayout()
            ->getBlock('related_catalogrule_banners_grid')
            ->setSelectedCatalogruleBanners($this->getRequest()->getPost('selected_catalogrule_banners'));
        $this->renderLayout();
    }
}
