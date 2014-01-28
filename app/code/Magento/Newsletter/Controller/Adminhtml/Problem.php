<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Controller\Adminhtml;

/**
 * Newsletter subscribers controller
 */
class Problem extends \Magento\Backend\App\Action
{
    /**
     * Newsletter problems report page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title->add(__('Newsletter Problems Report'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_view->getLayout()->getMessagesBlock()->setMessages($this->messageManager->getMessages(true));
        $this->_view->loadLayout();

        $this->_setActiveMenu('Magento_Newsletter::newsletter_problem');
        $this->_addBreadcrumb(__('Newsletter Problem Reports'), __('Newsletter Problem Reports'));

        $this->_view->renderLayout();
    }

    /**
     * Newsletter problems grid
     *
     * @return void
     */
    public function gridAction()
    {
        if ($this->getRequest()->getParam('_unsubscribe')) {
            $problems = (array) $this->getRequest()->getParam('problem', array());
            if (count($problems) > 0) {
                $collection = $this->_objectManager->create('Magento\Newsletter\Model\Resource\Problem\Collection');
                $collection
                    ->addSubscriberInfo()
                    ->addFieldToFilter($collection->getResource()->getIdFieldName(),
                                       array('in' => $problems))
                    ->load();

                $collection->walk('unsubscribe');
            }

            $this->messageManager->addSuccess(__('We unsubscribed the people you identified.'));
        }

        if ($this->getRequest()->getParam('_delete')) {
            $problems = (array) $this->getRequest()->getParam('problem', array());
            if (count($problems) > 0) {
                $collection = $this->_objectManager->create('Magento\Newsletter\Model\Resource\Problem\Collection');
                $collection->addFieldToFilter(
                    $collection->getResource()->getIdFieldName(),
                    array('in' => $problems)
                )->load();
                $collection->walk('delete');
            }

            $this->messageManager->addSuccess(__('The problems you identified have been deleted.'));
        }
        $this->_view->getLayout()->getMessagesBlock()->setMessages($this->messageManager->getMessages(true));
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * Check if user has enough privileges
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Newsletter::problem');
    }
}
