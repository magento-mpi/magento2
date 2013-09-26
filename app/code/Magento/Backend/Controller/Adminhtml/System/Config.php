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
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Controller\Adminhtml\System;

class Config extends \Magento\Backend\Controller\System\AbstractConfig
{
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
        $this->_title(__('Configuration'));

        $current = $this->getRequest()->getParam('section');
        $website = $this->getRequest()->getParam('website');
        $store   = $this->getRequest()->getParam('store');

        /** @var $configStructure \Magento\Backend\Model\Config\Structure */
        $configStructure = \Mage::getSingleton('Magento\Backend\Model\Config\Structure');
        /** @var $section \Magento\Backend\Model\Config\Structure\Element\Section */
        $section = $configStructure->getElement($current);
        if ($current && !$section->isVisible($website, $store)) {
            return $this->_redirect('*/*/', array('website' => $website, 'store' => $store));
        }

        $this->loadLayout();

        $this->_setActiveMenu('Magento_Adminhtml::system_config');
        $this->getLayout()->getBlock('menu')->setAdditionalCacheKeyInfo(array($current));

        $this->_addBreadcrumb(
            __('System'),
            __('System'),
            $this->getUrl('*\/system')
        );

        $this->renderLayout();
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
        $fileName   = 'tablerates.csv';
        /** @var $gridBlock \Magento\Adminhtml\Block\Shipping\Carrier\Tablerate\Grid */
        $gridBlock  = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Shipping\Carrier\Tablerate\Grid');
        $website    = \Mage::app()->getWebsite($this->getRequest()->getParam('website'));
        if ($this->getRequest()->getParam('conditionName')) {
            $conditionName = $this->getRequest()->getParam('conditionName');
        } else {
            $conditionName = $website->getConfig('carriers/tablerate/condition_name');
        }
        $gridBlock->setWebsiteId($website->getId())->setConditionName($conditionName);
        $content    = $gridBlock->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
}
