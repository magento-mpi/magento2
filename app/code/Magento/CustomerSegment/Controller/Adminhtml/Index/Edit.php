<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Index;

class Edit extends \Magento\CustomerSegment\Controller\Adminhtml\Index
{
    /**
     * Init active menu and set breadcrumb
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Magento_CustomerSegment::customer_customersegment'
        )->_addBreadcrumb(
            __('Segments'),
            __('Segments')
        );
        return $this;
    }

    /**
     * Edit customer segment
     *
     * @return void
     */
    public function execute()
    {
        $this->_title->add(__('Customer Segments'));

        try {
            $model = $this->_initSegment();
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('customersegment/*/');
            return;
        }

        $this->_title->add($model->getId() ? $model->getName() : __('New Segment'));

        // set entered data if was error when we do save
        $data = $this->_session->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $model->getConditions()->setJsFormObject('segment_conditions_fieldset');

        $this->_initAction();

        $block = $this->_view->getLayout()->createBlock(
            'Magento\CustomerSegment\Block\Adminhtml\Customersegment\Edit'
        )->setData(
            'form_action_url',
            $this->getUrl('customersegment/*/save')
        );

//        $this->_view->getLayout()->getBlock('head')->setCanLoadExtJs(true)->setCanLoadRulesJs(true);

        $this->_addBreadcrumb(
            $model->getId() ? __('Edit Segment') : __('New Segment'),
            $model->getId() ? __('Edit Segment') : __('New Segment')
        )->_addContent(
            $block
        )->_addLeft(
            $this->_view->getLayout()->createBlock('Magento\CustomerSegment\Block\Adminhtml\Customersegment\Edit\Tabs')
        );
        $this->_view->renderLayout();
    }
}
