<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Configuration controller
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Controller_Adminhtml_System_Config extends Mage_Backend_Controller_System_ConfigAbstract
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
        $this->_title($this->__('Configuration'));

        $current = $this->getRequest()->getParam('section');
        $website = $this->getRequest()->getParam('website');
        $store   = $this->getRequest()->getParam('store');

        /** @var $configStructure Mage_Backend_Model_Config_Structure */
        $configStructure = Mage::getSingleton('Mage_Backend_Model_Config_Structure');
        /** @var $section Mage_Backend_Model_Config_Structure_Element_Section */
        $section = $configStructure->getElement($current);
        if ($current && !$section->isVisible($website, $store)) {
            return $this->_redirect('*/*/', array('website' => $website, 'store' => $store));
        }

        $this->loadLayout();

        $this->_setActiveMenu('Mage_Adminhtml::system_config');
        $this->getLayout()->getBlock('menu')->setAdditionalCacheKeyInfo(array($current));

        $this->_addBreadcrumb(
            Mage::helper('Mage_Backend_Helper_Data')->__('System'),
            Mage::helper('Mage_Backend_Helper_Data')->__('System'),
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
        /** @var $gridBlock Mage_Adminhtml_Block_Shipping_Carrier_Tablerate_Grid */
        $gridBlock  = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Shipping_Carrier_Tablerate_Grid');
        $website    = Mage::app()->getWebsite($this->getRequest()->getParam('website'));
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
