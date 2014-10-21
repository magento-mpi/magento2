<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Controller\Adminhtml\Term;

use Magento\Search\Controller\Adminhtml\Search;

class Edit extends \Magento\Search\Controller\Adminhtml\Term
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
        $model = $this->_objectManager->create('Magento\Search\Model\Query');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This search no longer exists.'));
                $this->_redirect('search/*');
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
            'adminhtml.search.term.edit'
        )->setData(
            'action',
            $this->getUrl('search/term/save')
        );

        $this->_addBreadcrumb($id ? __('Edit Search') : __('New Search'), $id ? __('Edit Search') : __('New Search'));

        $this->_view->renderLayout();
    }
}
