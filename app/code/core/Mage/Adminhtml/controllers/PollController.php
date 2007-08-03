<?php
/**
 * Adminhtml poll manager controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_PollController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('cms/poll');
        $this->_addBreadcrumb(__('Poll Manager'), __('Poll Manager Title'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/poll_poll'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('cms/poll');
        $this->_addBreadcrumb(__('Poll Manager'), __('Poll Manager Title'), Mage::getUrl('*/*/'));
        $this->_addBreadcrumb(__('Edit Poll'), __('Edit Poll Title'));

        $this->getLayout()->getBlock('root')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('adminhtml/poll_edit'))
             ->_addLeft($this->getLayout()->createBlock('adminhtml/poll_edit_tabs'));

        $this->renderLayout();
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('poll/poll');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Poll was deleted succesfully'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(__('Unable to find a poll to delete'));
        $this->_redirect('*/*/');
    }

    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {
                $model = Mage::getModel('poll/poll');

                if( !$this->getRequest()->getParam('id') ) {
                    $model->setDatePosted(now());
                }

                if( $this->getRequest()->getParam('closed') && !$this->getRequest()->getParam('was_closed') ) {
                    $model->setDateClosed(now());
                }

                $model->setPollTitle($this->getRequest()->getParam('poll_title'))
                      ->setActive( (!$this->getRequest()->getParam('closed')) ? $this->getRequest()->getParam('active') : 0)
                      ->setClosed($this->getRequest()->getParam('closed'))
                      ->setId($this->getRequest()->getParam('id'));

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Poll succesfully saved.'));
                Mage::getSingleton('adminhtml/session')->setPollData(false);

                if( !$this->getRequest()->getParam('id') ) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), 'tab' => 'answers_section'));
                } else {
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPollData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function newAction()
    {
        $this->_forward('edit');
    }
}