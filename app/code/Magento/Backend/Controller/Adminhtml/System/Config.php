<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Configuration controller
 */
namespace Magento\Backend\Controller\Adminhtml\System;

class Config extends \Magento\Backend\Controller\Adminhtml\System\AbstractConfig
{
    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\Config\Structure $configStructure
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\Config\Structure $configStructure,
        \Magento\App\Response\Http\FileFactory $fileFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context, $configStructure);
    }

    /**
     * Index action
     *
     */
    public function indexAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit configuration section
     *
     */
    public function editAction()
    {
        $this->_title->add(__('Configuration'));

        $current = $this->getRequest()->getParam('section');
        $website = $this->getRequest()->getParam('website');
        $store   = $this->getRequest()->getParam('store');

        /** @var $section \Magento\Backend\Model\Config\Structure\Element\Section */
        $section = $this->_configStructure->getElement($current);
        if ($current && !$section->isVisible($website, $store)) {
            return $this->_redirect('adminhtml/*/', array('website' => $website, 'store' => $store));
        }

        $this->_view->loadLayout();

        $this->_setActiveMenu('Magento_Adminhtml::system_config');
        $this->_view->getLayout()->getBlock('menu')->setAdditionalCacheKeyInfo(array($current));

        $this->_addBreadcrumb(
            __('System'),
            __('System'),
            $this->getUrl('*\/system')
        );

        $this->_view->renderLayout();
    }

    /**
     * Save fieldset state through AJAX
     */
    public function stateAction()
    {
        if ($this->getRequest()->getParam('isAjax') && $this->getRequest()->getParam('container') != ''
            && $this->getRequest()->getParam('value') != ''
        ) {
            $configState = array(
                $this->getRequest()->getParam('container') => $this->getRequest()->getParam('value')
            );
            $this->_saveState($configState);
            $this->getResponse()->setBody('success');
        }
    }

    /**
     * Export shipping table rates in csv format
     */
    public function exportTableratesAction()
    {
        $fileName = 'tablerates.csv';
        /** @var $gridBlock \Magento\Shipping\Block\Adminhtml\Carrier\Tablerate\Grid */
        $gridBlock = $this->_view->getLayout()
            ->createBlock('Magento\Shipping\Block\Adminhtml\Carrier\Tablerate\Grid');
        $website = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'));
        if ($this->getRequest()->getParam('conditionName')) {
            $conditionName = $this->getRequest()->getParam('conditionName');
        } else {
            $conditionName = $website->getConfig('carriers/tablerate/condition_name');
        }
        $gridBlock->setWebsiteId($website->getId())->setConditionName($conditionName);
        $content = $gridBlock->getCsvFile();
        return $this->_fileFactory->create($fileName, $content, \Magento\App\Filesystem::VAR_DIR);
    }

    /**
     * Export Varnish Cofiguration as .vcl
     *
     * @return \Magento\App\ResponseInterface
     */
    public function exportVarnishConfigAction()
    {
        $fileName = 'varnish_configuration.vcl';
        $content = ''; // $varnish->getVclFile();
        return $this->_fileFactory->create($fileName, $content, \Magento\App\Filesystem::VAR_DIR);
    }
}
