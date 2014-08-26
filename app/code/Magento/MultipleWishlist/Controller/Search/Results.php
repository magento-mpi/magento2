<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\MultipleWishlist\Controller\Search;

class Results extends \Magento\MultipleWishlist\Controller\Search
{
    /**
     * Wishlist search action
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function execute()
    {
        $this->_view->loadLayout();

        try {
            $params = $this->getRequest()->getParam('params');
            if (empty($params) || !is_array($params) || empty($params['search'])) {
                throw new \Magento\Framework\Model\Exception(__('Please specify correct search options.'));
            }

            $strategy = null;
            switch ($params['search']) {
                case 'type':
                    $strategy = $this->_strategyNameFactory->create();
                    break;
                case 'email':
                    $strategy = $this->_strategyEmailFactory->create();
                    break;
                default:
                    throw new \Magento\Framework\Model\Exception(__('Please specify correct search options.'));
            }

            $strategy->setSearchParams($params);
            /** @var \Magento\MultipleWishlist\Model\Search $search */
            $search = $this->_searchFactory->create();
            $this->_coreRegistry->register('search_results', $search->getResults($strategy));
            $this->_customerSession->setLastWishlistSearchParams($params);
        } catch (\InvalidArgumentException $e) {
            $this->messageManager->addNotice($e->getMessage());
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We could not perform the search.'));
        }

        $layout = $this->_view->getLayout();
        $layout->initMessages();
        $this->pageConfig->setTitle(__('Wish List Search'));
        $this->_view->renderLayout();
    }
}
