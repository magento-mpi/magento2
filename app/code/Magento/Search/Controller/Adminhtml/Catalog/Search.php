<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Admin search controller(ajax grid)
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Controller\Adminhtml\Catalog;

class Search extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
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
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\CatalogSearch\Model\QueryFactory $queryFactory
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\CatalogSearch\Model\QueryFactory $queryFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_queryFactory = $queryFactory;
        parent::__construct($context);        
    }

    /**
     * Ajax grid action
     */
    public function relatedGridAction()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var \Magento\CatalogSearch\Model\Query $model */
        $model = $this->_queryFactory->create();
        $backendSession = $this->_objectManager->get('Magento\Adminhtml\Model\Session');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                $backendSession->addError(__('This search no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = $backendSession->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register('current_catalog_search', $model);

        $this->loadLayout(false);
        $this->renderLayout();
    }
}
