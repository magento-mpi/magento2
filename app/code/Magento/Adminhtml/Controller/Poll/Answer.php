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
 * Adminhtml poll answer controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Controller\Poll;

class Answer extends \Magento\Adminhtml\Controller\Action
{
    public function editAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('Magento_Poll::cms_poll');
        $this->_addBreadcrumb(__('Poll Manager'),
                              __('Poll Manager'), $this->getUrl('*/*/'));
        $this->_addBreadcrumb(__('Edit Poll Answer'),
                              __('Edit Poll Answer'));

        $this->_addContent($this->getLayout()->createBlock('Magento\Adminhtml\Block\Poll\Answer\Edit'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        //print '@@';
        if ( $post = $this->getRequest()->getPost() ) {
            try {
                $model = \Mage::getModel('Magento\Poll\Model\Poll\Answer');
                $model->setData($post)
                    ->setId($this->getRequest()->getParam('id'))
                    ->save();

                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addSuccess(
                    __('The answer has been saved.'));
                $this->_redirect('*/poll/edit',
                                 array('id' => $this->getRequest()->getParam('poll_id'), 'tab' => 'answers_section'));
                return;
            } catch (\Exception $e) {
                \Mage::getSingleton('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Magento\Adminhtml\Block\Poll\Edit\Tab\Answers\Grid')->toHtml()
        );
    }

    public function jsonSaveAction()
    {
        $response = new \Magento\Object();
        $response->setError(0);

        if ( $post = $this->getRequest()->getPost() ) {
            $data = \Zend_Json::decode($post['data']);
            try {
                if( trim($data['answer_title']) == '' ) {
                    throw new \Exception(__('Invalid Answer'));
                }
                $model = \Mage::getModel('Magento\Poll\Model\Poll\Answer');
                $model->setData($data)
                    ->save();
            } catch (\Exception $e) {
                $response->setError(1);
                $response->setMessage($e->getMessage());
            }
        }
        $this->getResponse()->setBody( $response->toJson() );
    }

    public function jsonDeleteAction()
    {
        $response = new \Magento\Object();
        $response->setError(0);

        if ( $id = $this->getRequest()->getParam('id') ) {
            try {
                $model = \Mage::getModel('Magento\Poll\Model\Poll\Answer');
                $model->setId(\Zend_Json::decode($id))
                    ->delete();
            } catch (\Exception $e) {
                $response->setError(1);
                $response->setMessage($e->getMessage());
            }
        } else {
            $response->setError(1);
            $response->setMessage(__('We can\'t find an answer to delete.'));
        }
        $this->getResponse()->setBody( $response->toJson() );
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Poll::poll');
    }

}
