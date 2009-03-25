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
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Events Adminhtml controller
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */

class Enterprise_CatalogEvent_Adminhtml_Catalog_EventController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init action breadcrumbs and active menu
     *
     * @return Enterprise_CatalogEvent_IndexController
     */
    public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('catalog')->__('Catalog'), Mage::helper('catalog')->__('Catalog'))
            ->_addBreadcrumb(Mage::helper('enterprise_catalogevent')->__('Events'), Mage::helper('enterprise_catalogevent')->__('Events'))
            ->_setActiveMenu('catalog/enterprise_catelogevent');
        return $this;
    }

    /**
     * Events list action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     * New event action
     *
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit event action
     */
    public function editAction()
    {
        $event = Mage::getModel('enterprise_catalogevent/event');
        if ($eventId = $this->getRequest()->getParam('event_id', false)) {
            $event->load($eventId);
        } else {
            $event->setCategoryId($this->getRequest()->getParam('category_id'));
        }

        Mage::register('enterprise_catalogevent_event', $event);

        $this->_initAction();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }

    /**
     * Save action
     *
     * @return void
     */
    public function saveAction()
    {
        $event = Mage::getModel('enterprise_catalogevent/event');
        /* @var $event Enterprise_CatalogEvent_Model_Event */
        if ($eventId = $this->getRequest()->getParam('event_id', false)) {
            $event->load($eventId);
        } else {
            $event->setCategoryId($this->getRequest()->getParam('category_id'));
        }

        $event->setDisplayState($this->getRequest()->getParam('display_state'))
            ->setDateStart($this->getRequest()->getParam('date_start'))
            ->setDateEnd($this->getRequest()->getParam('date_end'));

        try {
            $event->save();
            $this->_getSession()->addSuccess(
                Mage::helper('enterprise_catalogevent')->__('Event was successfully saved.')
            );
            if ($this->getRequest()->getParam('category')) {
                $this->_redirect('*/catalog_category/edit', array('id' => $event->getCategoryId()));
            } else {
                $this->_redirect('*/*/');
            }
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_getSession()->setEventData($this->getRequest()->getPost());
            $this->_redirect('*/*/edit', array('_current'=>true));
        }


    }

    /**
     * Delete action
     *
     * @return void
     */
    public function deleteAction()
    {
        $event = Mage::getModel('enterprise_catalogevent/event');
        $event->load($this->getRequest()->getParam('event_id', false));
        if ($event->getId()) {
            try {
                $event->delete();
                $this->_getSession()->addSuccess(
                    Mage::helper('enterprise_catalogevent')->__('Event was successfully deleted.')
                );
                if ($this->getRequest()->getParam('category')) {
                    $this->_redirect('*/catalog_category/edit', array('id' => $event->getCategoryId()));
                } else {
                    $this->_redirect('*/*/');
                }
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('_current'=>true));
            }
        }
    }

    /**
     * Ajax categories tree loader action
     *
     */
    public function categoriesJsonAction()
    {
        $id = $this->getRequest()->getParam('id', null);
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('enterprise_catalogevent/adminhtml_event_edit_category')
                ->getTreeArray($id, true, 1)
        );
    }

    /**
     * Acl check for admin
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/events');
    }

}
