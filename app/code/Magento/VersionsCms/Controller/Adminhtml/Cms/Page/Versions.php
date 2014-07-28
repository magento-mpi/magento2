<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page;

class Versions extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\VersionsCms\Model\PageLoader
     */
    protected $pageLoader;

    /**
     * {@inheritdoc}
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    }

    /**
     * Action for versions ajax tab
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Pages'));
        $this->pageLoader->load($this->_request->getParam('page_id'));

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
