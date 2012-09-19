<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

include_once "ProfileController.php";

/**
 * Convert GUI admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_System_Convert_GuiController extends Mage_Adminhtml_System_Convert_ProfileController
{
    /**
     * Profiles list action
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Import and Export'))
             ->_title($this->__('Profiles'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }
        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('Mage_Adminhtml::system_convert');

        /**
         * Append profiles block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_System_Convert_Gui', 'convert_profile')
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Import/Export'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('Import/Export'));
        $this->_addBreadcrumb(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Profiles'), Mage::helper('Mage_Adminhtml_Helper_Data')->__('Profiles'));

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_System_Convert_Gui_Grid')->toHtml()
        );
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
        $data = Mage::getSingleton('Mage_Adminhtml_Model_Session')->getConvertProfileData(true);

        if (!empty($data)) {
            $profile->addData($data);
        }

        $this->_title($profile->getId() ? $profile->getName() : $this->__('New Profile'));

        $this->_setActiveMenu('Mage_Adminhtml::system_convert');


        $this->_addContent(
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_System_Convert_Gui_Edit')
        );

        /**
         * Append edit tabs to left block
         */
        $this->_addLeft($this->getLayout()->createBlock('Mage_Adminhtml_Block_System_Convert_Gui_Edit_Tabs'));

        $this->renderLayout();
    }

    public function uploadAction()
    {
        $this->_initProfile();
        $profile = Mage::registry('current_convert_profile');
    }

    public function uploadPostAction()
    {
        $this->_initProfile();
        $profile = Mage::registry('current_convert_profile');
    }

    public function downloadAction()
    {
        $filename = $this->getRequest()->getParam('filename');
        if (!$filename || strpos($filename, '..')!==false || $filename[0]==='.') {
            return;
        }
        $this->_initProfile();
        $profile = Mage::registry('current_convert_profile');
    }

    protected function _isAllowed()
    {
//        switch ($this->getRequest()->getActionName()) {
//            case 'index':
//                $aclResource = 'admin/system/convert/gui';
//                break;
//            case 'grid':
//                $aclResource = 'admin/system/convert/gui';
//                break;
//            case 'run':
//                $aclResource = 'admin/system/convert/gui/run';
//                break;
//            default:
//                $aclResource = 'admin/system/convert/gui/edit';
//                break;
//        }

        return Mage::getSingleton('Mage_Core_Model_Authorization')->isAllowed('Mage_Adminhtml::gui');
    }
}
