<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Controller\Adminhtml;

/**
 * Admin search controller for Ajax Grid in Catalog Search Terms
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Search extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Query factory
     *
     * @var \Magento\CatalogSearch\Model\QueryFactory
     */
    protected $_queryFactory;

    /**
     * Construct
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\CatalogSearch\Model\QueryFactory $queryFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\CatalogSearch\Model\QueryFactory $queryFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_queryFactory = $queryFactory;
        parent::__construct($context);
    }

    /**
     * Ajax grid action
     *
     * @return void
     */
    public function relatedGridAction()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\CatalogSearch\Model\Query $model */
        $model = $this->_queryFactory->create();
        $backendSession = $this->_objectManager->get('Magento\Backend\Model\Session');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This search no longer exists.'));
                $this->_redirect('adminhtml/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = $backendSession->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register('current_catalog_search', $model);

        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
