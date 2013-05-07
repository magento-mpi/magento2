<?php
/**
 * Controller class for Saas search index functionality
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Index_Adminhtml_Saas_IndexController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Saas_Index_Model_Flag
     */
    protected $_flag;

    /**
     * @param Mage_Backend_Controller_Context $context
     * @param Saas_Index_Model_FlagFactory $flagFactory
     * @param string $areaCode
     */
    public function __construct(
        Mage_Backend_Controller_Context $context,
        Saas_Index_Model_FlagFactory $flagFactory,
        $areaCode = null
    ) {
        parent::__construct($context,$areaCode);
        $this->_flag = $flagFactory->create();
        $this->_flag->loadSelf();
    }

    /**
     * Display search index form
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))->_title($this->__('Refresh Search Index'));
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Put task for search index refresh into the queue
     */
    public function refreshAction()
    {
        if (!$this->getRequest()->isAjax()) {
            return $this->_redirect('*/*/index');
        }

        $this->_eventManager->dispatch('application_process_refresh_catalog');

        $this->_flag->setState(Saas_Index_Model_Flag::STATE_QUEUED);
        $this->_flag->save();

        $this->_endAjax(array(
            'error'       => false,
            'message'     => '', //here you can add error message if needed
            'status_html' => $this->_getStatusHtml(),
        ));
    }

    /**
     * Update index status action
     */
    public function updateStatusAction()
    {
        $this->_endAjax(array(
            'status_html' => $this->_getStatusHtml(),
            'is_finished' => $this->_flag->isTaskFinished() || $this->_flag->isTaskNotified(),
        ));
    }

    /**
     * Get index status in html
     *
     * @return string
     */
    protected function _getStatusHtml()
    {
        $block = $this->getLayout()->createBlock('Saas_Index_Block_Backend_Index_Status');
        return $block ? $block->toHtml() : '';
    }

    /**
     * Set body content for ajax request
     *
     * @param array $res
     */
    protected function _endAjax($res)
    {
        $helper = $this->_objectManager->get('Mage_Core_Helper_Data');
        $responseContent = $helper->jsonEncode($res);
        $this->getResponse()->setBody($responseContent);
    }

    /**
     * Check ACL permissions
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mage_Index::index');
    }
}
