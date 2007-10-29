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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_System_Convert_ProfileController extends Mage_Adminhtml_Controller_Action
{
    protected function _initProfile($idFieldName = 'id')
    {
        $profileId = (int) $this->getRequest()->getParam($idFieldName);
        $profile = Mage::getModel('core/convert_profile');

        if ($profileId) {
            $profile->load($profileId);
        }

        Mage::register('current_convert_profile', $profile);

        return $this;
    }

    /**
     * Profiles list action
     */
    public function indexAction()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('system/convert');

        /**
         * Append profiles block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_profile', 'convert_profile')
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(__('Import/Export Profiles'), __('Import/Export Profiles'));
        $this->_addBreadcrumb(__('Manage Profiles'), __('Manage Profiles'));

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/system_convert_profile_grid')->toHtml());
    }

    /**
     * Profile edit action
     */
    public function editAction()
    {
        $this->_initProfile();
        $this->loadLayout();

        $profile = Mage::registry('current_convert_profile');

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getConvertProfileData(true);

        if (!empty($data)) {
            $profile->addData($data);
        }

        $this->_setActiveMenu('system/convert');


        $this->_addContent(
            $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit')
        );

        $this->renderLayout();
    }

    /**
     * Create new profile action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Delete profile action
     */
    public function deleteAction()
    {
        $this->_initProfile();
        $profile = Mage::registry('current_convert_profile');
        if ($profile->getId()) {
            try {
                $profile->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Profile was deleted'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*');
    }

    /**
     * Save profile action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $this->_initProfile('profile_id');
            $profile = Mage::registry('current_convert_profile');

            // Prepare profile saving data
            if (isset($data)) {
                $profile->addData($data);
            }

            try {
                $profile->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(__('Profile was successfully saved'));
            }
            catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setConvertProfileData($data);
                $this->getResponse()->setRedirect(Mage::getUrl('*/*/edit', array('id'=>$profile->getId())));
                return;
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('*/*'));
    }

    public function runAction()
    {

    }

    protected function _isAllowed()
    {
    	//print $this->getRequest()->getActionName();
	    return Mage::getSingleton('admin/session')->isAllowed('system/tools/convert');
    }
}
