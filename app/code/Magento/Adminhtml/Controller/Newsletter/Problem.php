<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter subscribers controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Newsletter_Problem extends Magento_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Newsletter Problems Report'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->getLayout()->getMessagesBlock()->setMessages(
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->getMessages(true)
        );
        $this->loadLayout();

        $this->_setActiveMenu('Mage_Newsletter::newsletter_problem');

        $this->_addBreadcrumb(Mage::helper('Mage_Newsletter_Helper_Data')->__('Newsletter Problem Reports'), Mage::helper('Mage_Newsletter_Helper_Data')->__('Newsletter Problem Reports'));

        $this->renderLayout();
    }

    public function gridAction()
    {
        if($this->getRequest()->getParam('_unsubscribe')) {
            $problems = (array) $this->getRequest()->getParam('problem', array());
            if (count($problems)>0) {
                $collection = Mage::getResourceModel('Mage_Newsletter_Model_Resource_Problem_Collection');
                $collection
                    ->addSubscriberInfo()
                    ->addFieldToFilter($collection->getResource()->getIdFieldName(),
                                       array('in'=>$problems))
                    ->load();

                $collection->walk('unsubscribe');
            }

            Mage::getSingleton('Magento_Adminhtml_Model_Session')
                ->addSuccess(Mage::helper('Mage_Newsletter_Helper_Data')->__('We unsubscribed the people you identified.'));
        }

        if($this->getRequest()->getParam('_delete')) {
            $problems = (array) $this->getRequest()->getParam('problem', array());
            if (count($problems)>0) {
                $collection = Mage::getResourceModel('Mage_Newsletter_Model_Resource_Problem_Collection');
                $collection
                    ->addFieldToFilter($collection->getResource()->getIdFieldName(),
                                       array('in'=>$problems))
                    ->load();
                $collection->walk('delete');
            }

            Mage::getSingleton('Magento_Adminhtml_Model_Session')
                ->addSuccess(Mage::helper('Mage_Newsletter_Helper_Data')->__('The problems you identified have been deleted.'));
        }
                $this->getLayout()->getMessagesBlock()->setMessages(Mage::getSingleton('Magento_Adminhtml_Model_Session')->getMessages(true));

        $this->loadLayout(false);
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Newsletter::problem');
    }
}
