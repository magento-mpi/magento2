<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Controller\Adminhtml\System;

use Magento\App\ResponseInterface;

/**
 * System Configuration controller
 */
class Config extends AbstractConfig
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
     * @return void
     */
    public function indexAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit configuration section
     *
     * @return void
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

        $this->_setActiveMenu('Magento_Backend::system_config');
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
     *
     * @return void
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
}
