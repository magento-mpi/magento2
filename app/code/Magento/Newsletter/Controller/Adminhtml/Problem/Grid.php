<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Newsletter\Controller\Adminhtml\Problem;

class Grid extends \Magento\Newsletter\Controller\Adminhtml\Problem
{
    /**
     * Newsletter problems grid
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('_unsubscribe')) {
            $problems = (array)$this->getRequest()->getParam('problem', []);
            if (count($problems) > 0) {
                $collection = $this->_objectManager->create('Magento\Newsletter\Model\Resource\Problem\Collection');
                $collection->addSubscriberInfo()->addFieldToFilter(
                    $collection->getResource()->getIdFieldName(),
                    ['in' => $problems]
                )->load();

                $collection->walk('unsubscribe');
            }

            $this->messageManager->addSuccess(__('We unsubscribed the people you identified.'));
        }

        if ($this->getRequest()->getParam('_delete')) {
            $problems = (array)$this->getRequest()->getParam('problem', []);
            if (count($problems) > 0) {
                $collection = $this->_objectManager->create('Magento\Newsletter\Model\Resource\Problem\Collection');
                $collection->addFieldToFilter(
                    $collection->getResource()->getIdFieldName(),
                    ['in' => $problems]
                )->load();
                $collection->walk('delete');
            }

            $this->messageManager->addSuccess(__('The problems you identified have been deleted.'));
        }
        $this->_view->loadLayout(false);
        $this->_view->getLayout()->getMessagesBlock()->setMessages($this->messageManager->getMessages(true));
        $this->_view->renderLayout();
    }
}
