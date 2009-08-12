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
 * @package    Enterprise_CustomerSegment
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_CustomerSegment_Adminhtml_CustomersegmentController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Segments list
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('customer/customersegment');
        $this->renderLayout();    
    }

    /**
     * Create new customer segment
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit customer segment
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('enterprise_customersegment/segment');

        if ($id) {
            $model->load($id);
            if (! $model->getSegmentId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('enterprise_customersegment')->__('This segment no longer exists'));
                $this->_redirect('*/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        
        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        Mage::register('enterprise_customersegment_segment', $model);

        $block =  $this->getLayout()->createBlock(
            'enterprise_customersegment/adminhtml_customersegment_edit')
            ->setData('form_action_url', $this->getUrl('*/*/save'));        

        $this->_initAction();
        
        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true);
        
        $this
            ->_addBreadcrumb($id ? Mage::helper('enterprise_customersegment')->__('Edit Segment') : Mage::helper('enterprise_customersegment')->__('New Segment'), $id ? Mage::helper('enterprise_customersegment')->__('Edit Segment') : Mage::helper('enterprise_customersegment')->__('New Segment'))
            ->_addContent($block)
            ->_addLeft($this->getLayout()->createBlock('enterprise_customersegment/adminhtml_customersegment_edit_tabs'))
            ->renderLayout();
    }
    
    /**
     * Init active menu and set breadcrumb
     *
     * @return Enterprise_CustomerSegment_Adminhtml_CustomersegmentController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('customer/customersegment')
            ->_addBreadcrumb(Mage::helper('enterprise_customersegment')->__('Segments'), Mage::helper('enterprise_customersegment')->__('Segments'))
        ;
        return $this;
    }

    /**
     * Add new condition
     */
    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];
        
        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('enterprise_customersegment/segment'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Save customer segment
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $redirectBack = $this->getRequest()->getParam('back', false);
                $model = Mage::getModel('enterprise_customersegment/segment');
                if ($id = $this->getRequest()->getParam('segment_id')) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        Mage::throwException(Mage::helper('enterprise_customersegment')->__('Wrong rule specified.'));
                    }
                }
                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);

                $model->loadPost($data);
                Mage::getSingleton('adminhtml/session')->setPageData($model->getData());
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('enterprise_customersegment')->__('Segment was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPageData(false);

                if ($redirectBack) {
                  $this->_redirect('*/*/' . $redirectBack, array(
                        'id' => $model->getId()
                  ));
                  return;
                }
                
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('segment_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete customer segment
     */
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('enterprise_customersegment/segment');
                $model->load($id);
                $model->delete();
                Mage::app()->saveCache(1, 'enterprise_customersegment_segment_dirty');
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('enterprise_customersegment')->__('Segment was successfully deleted'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('enterprise_customersegment')->__('Unable to find a page to delete'));
        $this->_redirect('*/*/');
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/customersegment') &&
            Mage::helper('enterprise_customersegment')->isEnabled();
    }
    
}
