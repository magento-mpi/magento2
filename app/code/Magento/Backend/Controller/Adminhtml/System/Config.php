<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System;

/**
 * System Configuration controller
 */
class Config extends AbstractConfig
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Backend\Model\Config
     */
    protected $_backendConfig;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Config\Structure $configStructure
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Backend\Model\Config $backendConfig
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Config\Structure $configStructure,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Config $backendConfig
    ) {
        parent::__construct($context, $configStructure);
        $this->_storeManager = $storeManager;
        $this->_fileFactory = $fileFactory;
        $this->_backendConfig = $backendConfig;
    }

    /**
     * Set scope to backend config
     *
     * @param string $sectionId
     * @return bool
     */
    protected function _isSectionAllowed($sectionId)
    {
        $website = $this->getRequest()->getParam('website');
        $store = $this->getRequest()->getParam('store');
        if ($store) {
            $this->_backendConfig->setStore($store);
        } elseif ($website) {
            $this->_backendConfig->setWebsite($website);
        }
        return parent::_isSectionAllowed($sectionId);
    }

    /**
     * Index action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit configuration section
     *
     * @return \Magento\Framework\App\ResponseInterface|void
     */
    public function editAction()
    {
        $this->_title->add(__('Configuration'));

        $current = $this->getRequest()->getParam('section');
        $website = $this->getRequest()->getParam('website');
        $store = $this->getRequest()->getParam('store');

        /** @var $section \Magento\Backend\Model\Config\Structure\Element\Section */
        $section = $this->_configStructure->getElement($current);
        if ($current && !$section->isVisible($website, $store)) {
            return $this->_redirect('adminhtml/*/', array('website' => $website, 'store' => $store));
        }

        $this->_view->loadLayout();

        $this->_setActiveMenu('Magento_Backend::system_config');
        $this->_view->getLayout()->getBlock('menu')->setAdditionalCacheKeyInfo(array($current));

        $this->_addBreadcrumb(__('System'), __('System'), $this->getUrl('*\/system'));

        $this->_view->renderLayout();
    }

    /**
     * Save fieldset state through AJAX
     *
     * @return void
     */
    public function stateAction()
    {
        if ($this->getRequest()->getParam(
            'isAjax'
        ) && $this->getRequest()->getParam(
            'container'
        ) != '' && $this->getRequest()->getParam(
            'value'
        ) != ''
        ) {
            $configState = array($this->getRequest()->getParam('container') => $this->getRequest()->getParam('value'));
            $this->_saveState($configState);
            $this->getResponse()->setBody('success');
        }
    }
}
