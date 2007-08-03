<?php
/**
 * Adminhtml poll answer controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Poll_AnswerController extends Mage_Adminhtml_Controller_Action
{
    public function editAction()
    {
        $this->loadLayout('baseframe');

        $this->_setActiveMenu('cms/poll');
        $this->_addBreadcrumb(__('Poll Manager'), __('Poll Manager Title'), Mage::getUrl('*/*/'));
        $this->_addBreadcrumb(__('Edit Poll Answer'), __('Edit Poll Answer'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/poll_answer_edit'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        if ( $post = $this->getRequest()->getPost() ) {
            try {
                $model = Mage::getModel('poll/poll_answer');
                $model->setData($post)
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(__('Answer was saved succesfully'));
                $this->_redirect('*/poll/edit', array('id' => $this->getRequest()->getParam('poll_id'), 'tab' => 'answers_section'));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/poll_edit_tab_answers_grid')->toHtml());
    }

    public function jsonSaveAction()
    {
        $response = new Varien_Object();
        $response->setError(0);

        if ( $post = $this->getRequest()->getPost() ) {
            $data = Zend_Json_Decoder::decode($post['data']);
            try {
                if( trim($data['answer_title']) == '' ) {
                    throw new Exception(__('Invalid Answer Title'));
                }
                $model = Mage::getModel('poll/poll_answer');
                $model->setData($data)
                    ->save();
            } catch (Exception $e) {
                $response->setError(1);
                $response->setMessage($e->getMessage());
            }
        }
        $this->getResponse()->setBody( $response->toJson() );
    }

    public function jsonDeleteAction()
    {
        $response = new Varien_Object();
        $response->setError(0);

        if ( $id = $this->getRequest()->getParam('id') ) {
            try {
                $model = Mage::getModel('poll/poll_answer');
                $model->setId(Zend_Json_Decoder::decode($id))
                    ->delete();
            } catch (Exception $e) {
                $response->setError(1);
                $response->setMessage($e->getMessage());
            }
        } else {
            $response->setError(1);
            $response->setMessage(__('Unable to find answer to delete.'));
        }
        $this->getResponse()->setBody( $response->toJson() );
    }
}