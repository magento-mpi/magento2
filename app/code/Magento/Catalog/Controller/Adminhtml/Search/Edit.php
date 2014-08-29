<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Search;

class Edit extends \Magento\Catalog\Controller\Adminhtml\Search
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Search Terms'));

        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magento\CatalogSearch\Model\Query');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This search no longer exists.'));
                $this->_redirect('catalog/*');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_coreRegistry->register('current_catalog_search', $model);

        $this->_initAction();

        $this->_title->add($id ? $model->getQueryText() : __('New Search'));

        $this->_view->getLayout()->getBlock(
            'adminhtml.catalog.search.edit'
        )->setData(
            'action',
            $this->getUrl('catalog/search/save')
        );

        $this->_addBreadcrumb($id ? __('Edit Search') : __('New Search'), $id ? __('Edit Search') : __('New Search'));

        $this->_view->renderLayout();
    }
}
