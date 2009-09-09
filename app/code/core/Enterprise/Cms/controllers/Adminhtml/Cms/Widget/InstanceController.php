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
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Admihtml Manage Cms Widgets Controller
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Adminhtml_Cms_Widget_InstanceController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Getter
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * Load layout, set active menu and breadcrumbs
     *
     * @return Enterprise_Cms_Adminhtml_Cms_Widget_InstanceController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cms/widgets')
            ->_addBreadcrumb(Mage::helper('enterprise_cms')->__('CMS'),
                Mage::helper('enterprise_cms')->__('CMS'))
            ->_addBreadcrumb(Mage::helper('enterprise_cms')->__('Manage Widget Instances'),
                Mage::helper('enterprise_cms')->__('Manage Widget Instances'));
        return $this;
    }

    /**
     * Init widget instance object and set it to registry
     *
     * @return Enterprise_Cms_Model_Widget_Instance
     */
    protected function _initWidgetInstance()
    {
        $widgetInstance = Mage::getModel('enterprise_cms/widget_instance');
        $instanceId = $this->getRequest()->getParam('instance_id', null);
        $type = $this->getRequest()->getParam('type', null);
        $packageTheme = $this->getRequest()->getParam('package_theme', null);
        if ($instanceId) {
            $widgetInstance->load($instanceId);
            if (!$widgetInstance->getId()) {
                $this->_getSession()->addError(Mage::helper('enterprise_cms')->__('Widget instance is no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
            $data['type'] = $widgetInstance->getType();
            $data['package_theme'] = $widgetInstance->getPackageTheme();
        } else {
            $widgetInstance->setType($type)
                ->setPackageTheme($packageTheme);
        }
        Mage::register('widget_instance', $widgetInstance);
        return $widgetInstance;
    }

    protected function _initWidgetInstanceForSave()
    {
        $widgetInstance = $this->_initWidgetInstance();
        $data = $this->getRequest()->getPost();
        $widgetInstance->setData($data);
        return $widgetInstance;
    }

    /**
     * Widget Instances Grid
     *
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * New widget instance action (forward to edit action)
     *
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit widget instance action
     *
     */
    public function editAction()
    {
        $widgetInstance = $this->_initWidgetInstance();
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * Validate action
     *
     */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);
        $this->getResponse()->setBody($response->toJson());
    }

    /**
     * Save action
     *
     */
    public function saveAction()
    {
        $widgetInstance = $this->_initWidgetInstance();
        $widgetTitle = $this->getRequest()->getPost('title');
        $storeIds = $this->getRequest()->getPost('store_ids', array(0));
        $widgetInstanceData = $this->getRequest()->getPost('widget_instance');
        $widgetParameters = $this->getRequest()->getPost('parameters');
        $widgetInstance->setTitle($widgetTitle)
            ->setStoreIds($storeIds)
            ->setPageGroups($widgetInstanceData)
            ->setWidgetParameters($widgetParameters);
        try {
            $widgetInstance->save();
            if ($this->getRequest()->getParam('back', false)) {
                    $this->_redirect('*/*/edit', array('instance_id' => $widgetInstance->getId(), '_current' => true));
            } else {
                $this->_redirect('*/*/');
            }
            return;
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('_current' => true));
            return;
        }
        $this->_redirect('*/*/');
        return;
    }

    public function gridAction()
    {
        $gridType = $this->getRequest()->getParam('gridType', null);
        if ($gridType) {
            $this->getResponse()->setBody($gridType);
        }
    }

    /**
     * Blocks Action (Ajax request)
     *
     */
    public function blocksAction()
    {
        /* @var $widgetInstance Enterprise_Cms_Model_Widget_Instance */
        $widgetInstance = $this->_initWidgetInstance();
        $layout = $this->getRequest()->getParam('layout');
        $selected = $this->getRequest()->getParam('selected', null);
        $blocksChooser = $this->getLayout()
            ->createBlock('enterprise_cms/adminhtml_cms_widget_instance_edit_chooser_block')
            ->setArea($widgetInstance->getArea())
            ->setPackage($widgetInstance->getPackage())
            ->setTheme($widgetInstance->getTheme())
            ->setLayoutHandle($layout)
            ->setSelected($selected);
        $html = $blocksChooser->toHtml();
        $this->getResponse()->setBody($html);
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/widget_instance');
    }
}
