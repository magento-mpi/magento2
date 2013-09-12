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
 * Adminhtml poll manager controller
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Controller_Poll extends Magento_Adminhtml_Controller_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    public function indexAction()
    {
        $this->_title(__('Polls'));

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Poll::cms_poll');
        $this->_addBreadcrumb(__('Poll Manager'), __('Poll Manager'));

        $this->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_Poll_Poll'));
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_title(__('Polls'));

        $pollId     = $this->getRequest()->getParam('id');
        $pollModel  = Mage::getModel('Magento_Poll_Model_Poll')->load($pollId);

        if ($pollModel->getId() || $pollId == 0) {
            $this->_title($pollModel->getId() ? $pollModel->getPollTitle() : __('New Poll'));

            $this->_coreRegistry->register('poll_data', $pollModel);

            $this->loadLayout();
            $this->_setActiveMenu('Magento_Poll::cms_poll');
            $this->_addBreadcrumb(__('Poll Manager'), __('Poll Manager'), $this->getUrl('*/*/'));
            $this->_addBreadcrumb(__('Edit Poll'), __('Edit Poll'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('Magento_Adminhtml_Block_Poll_Edit'))
                ->_addLeft($this->getLayout()->createBlock('Magento_Adminhtml_Block_Poll_Edit_Tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('The poll does not exist.'));
            $this->_redirect('*/*/');
        }
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = Mage::getModel('Magento_Poll_Model_Poll');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('You deleted the poll.'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(__('We can\'t find a poll to delete.'));
        $this->_redirect('*/*/');
    }

    public function saveAction()
    {
        Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(__('You saved the poll.'));
        Mage::getSingleton('Magento_Adminhtml_Model_Session')->setPollData(false);
        $this->_redirect('*/*/');
    }

    public function newAction()
    {
        $this->getRequest()->setParam('id', 0);
        $this->_forward('edit');
    }

    public function validateAction()
    {
        $response = new Magento_Object();
        $response->setError(false);

        if ($this->getRequest()->getPost()) {
            try {
                $pollModel = Mage::getModel('Magento_Poll_Model_Poll');

                if (!$this->getRequest()->getParam('id')) {
                    $pollModel->setDatePosted(now());
                }

                if ($this->getRequest()->getParam('closed') && !$this->getRequest()->getParam('was_closed')) {
                    $pollModel->setDateClosed(now());
                }

                if (!$this->getRequest()->getParam('closed')) {
                    $pollModel->setDateClosed(new Zend_Db_Expr('null'));
                }

                $pollModel->setPollTitle($this->getRequest()->getParam('poll_title'))
                      ->setClosed($this->getRequest()->getParam('closed'));

                if ($this->getRequest()->getParam('id') > 0) {
                    $pollModel->setId($this->getRequest()->getParam('id'));
                }

                $stores = $this->getRequest()->getParam('store_ids');
                if (!is_array($stores) || count($stores) == 0) {
                    Mage::throwException(__('Please indicate where this poll can be seen ("Visible In").'));
                }

                if (is_array($stores)) {
                    $storeIds = array();
                    foreach ($stores as $storeIdList) {
                        $storeIdList = explode(',', $storeIdList);
                        if (!$storeIdList) {
                            continue;
                        }
                        foreach ($storeIdList as $storeId) {
                            if ($storeId > 0) {
                                $storeIds[] = $storeId;
                            }
                        }
                    }
                    if (count($storeIds) === 0) {
                        Mage::throwException(__('Please indicate where this poll can be seen ("Visible In").'));
                    }
                    $pollModel->setStoreIds($storeIds);
                }

                $answers = $this->getRequest()->getParam('answer');

                if (!is_array($answers) || sizeof($answers) == 0) {
                    Mage::throwException(__('Please enter answer options for this poll.'));
                }

                if (is_array($answers)) {
                    $_titles = array();
                    foreach( $answers as $key => $answer ) {
                        if( in_array($answer['title'], $_titles) ) {
                            Mage::throwException(__('Your answers contain duplicates.'));
                        }
                        $_titles[] = $answer['title'];

                        $answerModel = Mage::getModel('Magento_Poll_Model_Poll_Answer');
                        if( intval($key) > 0 ) {
                            $answerModel->setId($key);
                        }
                        $answerModel->setAnswerTitle($answer['title'])
                            ->setVotesCount($answer['votes']);

                        $pollModel->addAnswer($answerModel);
                    }
                }

                $pollModel->save();

                $this->_coreRegistry->register('current_poll_model', $pollModel);

                $answersDelete = $this->getRequest()->getParam('deleteAnswer');
                if (is_array($answersDelete)) {
                    foreach ($answersDelete as $answer) {
                        $answerModel = Mage::getModel('Magento_Poll_Model_Poll_Answer');
                        $answerModel->setId($answer)
                            ->delete();
                    }
                }
            } catch (Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
                $this->_initLayoutMessages('Magento_Adminhtml_Model_Session');
                $response->setError(true);
                $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
            }
        }
        $this->getResponse()->setBody($response->toJson());
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Poll::poll');
    }
}
