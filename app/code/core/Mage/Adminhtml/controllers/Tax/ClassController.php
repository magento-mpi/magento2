<?php
/**
 * Adminhtml common tax class controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Tax_ClassController extends Mage_Adminhtml_Controller_Action
{
    public function saveAction()
    {
        if( $postData = $this->getRequest()->getPost() ) {
            $class = Mage::getModel('tax/class');
            $class->setData($postData);

            if($class->itemExists() === false) {
                try {
                    $class->save();
                    $classId = $class->getClassId();
                    $classType = $class->getClassType();
                    $this->_redirect("adminhtml/tax_class/edit/classId/{$classId}/classType/{$classType}");
                } catch (Exception $e) {
                    if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                        $this->getResponse()->setRedirect($referer);
                    }
                    Mage::getSingleton('adminhtml/session')->addError('Error wile saving this tax class. Please, try again later.');
                    $this->_returnLocation();
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError('Error wile saving this tax class. Class with the same name already exists.');
                $this->_returnLocation();
            }
        }
    }

    public function editAction()
    {
        $classType = strtolower($this->getRequest()->getParam('classType'));
        $classTypePhrase = ucfirst($classType);

        $this->_initAction()
            ->_addBreadcrumb(__("{$classTypePhrase} Tax Classes"), __("{$classTypePhrase} Tax Classes Title"), Mage::getUrl('adminhtml/tax_class_'.$classType))
            ->_addBreadcrumb(__("Edit {$classTypePhrase} Tax Class"), __("Edit {$classTypePhrase} Tax Class Title"))
            ->_addContent($this->getLayout()->createBlock('adminhtml/tax_class_page_edit'))
        ;

        $this->renderLayout();
    }

    public function deleteAction()
    {
        try {
            $classId = $this->getRequest()->getParam('classId');
            $classType = strtolower($this->getRequest()->getParam('classType'));

            $class = Mage::getSingleton('tax/class');
            $class->setClassId($classId);
            $class->delete();
            $this->getResponse()->setRedirect(Mage::getUrl("adminhtml/tax_class_{$classType}"));
        } catch (Exception $e) {
            if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                $this->getResponse()->setRedirect($referer);
            }
            Mage::getSingleton('adminhtml/session')->addError('Error wile deleting this tax class. Please, try again later.');
            $this->_returnLocation();
        }
    }

    public function saveGroupAction()
    {
        if( $postData = $this->getRequest()->getPost() ) {
            $group = Mage::getModel('tax/class_group');
            $group->setData($postData);
            try {
                $group->save();
                $classId = $this->getRequest()->getParam('classId');
                $classType = $this->getRequest()->getParam('classType');
                $this->_redirect("adminhtml/tax_class/edit/classId/{$classId}/classType/{$classType}");
            } catch ( Exception $e ) {
                if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                    $this->getResponse()->setRedirect($referer);
                }
                Mage::getSingleton('adminhtml/session')->addError('Error wile adding a group. Please, try again later.');
                $this->_returnLocation();
            }
        }
    }

    public function deleteGroupAction()
    {
        $groupId = $this->getRequest()->getParam('groupId');
        $classId = $this->getRequest()->getParam('classId');
        $classType = $this->getRequest()->getParam('classType');

        try {
            $group = Mage::getModel('tax/class_group');
            $group->setGroupId($groupId);
            $group->delete();
            $this->getResponse()->setRedirect(Mage::getUrl("adminhtml/tax_class/edit/classId/{$classId}/classType/{$classType}"));
        } catch (Exception $e) {
            if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                $this->getResponse()->setRedirect($referer);
            }
            Mage::getSingleton('adminhtml/session')->addError('Error wile deleting a group. Please, try again later.');
            $this->_returnLocation();
        }
     }

    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $classType = strtolower($this->getRequest()->getParam('classType'));
        $this->loadLayout('baseframe')
            ->_setActiveMenu('sales/tax/tax_classes_' . $classType)
            ->_addBreadcrumb(__('Sales'), __('Sales Title'))
            ->_addBreadcrumb(__('Tax'), __('Tax Title'))
            ->_addLeft(
                $this->getLayout()->createBlock('adminhtml/tax_tabs', 'tax_tabs')
                    ->setActiveTab('tax_class_' . $classType)
            )
        ;

        return $this;
    }

    protected function _returnLocation()
    {
        if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
            $this->getResponse()->setRedirect($referer);
        }
    }
}