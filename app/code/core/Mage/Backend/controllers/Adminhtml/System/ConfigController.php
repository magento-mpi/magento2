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
class Mage_Backend_Adminhtml_System_ConfigController extends Mage_Backend_Adminhtml_System_ConfigAbstract
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
        $this->_title($this->__('System'))->_title($this->__('Configuration'));

        $current = $this->getRequest()->getParam('section');
        $website = $this->getRequest()->getParam('website');
        $store   = $this->getRequest()->getParam('store');

        /** @var $systemConfig Mage_Backend_Model_Config_Structure */
        $systemConfig = Mage::getSingleton('Mage_Backend_Model_Config_Structure_Reader')->getConfiguration();

        $sections     = $systemConfig->getSections($current);
        $section      = isset($sections[$current]) ? $sections[$current] : array();
        $hasChildren  = $systemConfig->hasChildren($section, $website, $store);
        if (!$hasChildren && $current) {
            $this->_redirect('*/*/', array('website'=>$website, 'store'=>$store));
        }

        $this->loadLayout();

        $this->_setActiveMenu('Mage_Adminhtml::system_config');
        $this->getLayout()->getBlock('menu')->setAdditionalCacheKeyInfo(array($current));

        $this->_addBreadcrumb(
            Mage::helper('Mage_Backend_Helper_Data')->__('System'),
            Mage::helper('Mage_Backend_Helper_Data')->__('System'),
            $this->getUrl('*/system')
        );

        $this->getLayout()->addBlock('Mage_Backend_Block_System_Config_Tabs', '', 'left')->initTabs();

        if ($this->_isSectionAllowed) {
            $this->_addContent($this->getLayout()->createBlock('Mage_Backend_Block_System_Config_Edit')->initForm());

            $this->_addJs($this->getLayout()
                ->createBlock('Mage_Backend_Block_Template')
                ->setTemplate('Mage_Adminhtml::system/shipping/ups.phtml'));
            $this->_addJs($this->getLayout()
                ->createBlock('Mage_Backend_Block_Template')
                ->setTemplate('system/config/js.phtml'));
            $this->_addJs($this->getLayout()
                ->createBlock('Mage_Backend_Block_Template')
                ->setTemplate('Mage_Adminhtml::system/shipping/applicable_country.phtml'));

            $this->renderLayout();
        }
    }

    /**
     * Save fieldset state through AJAX
     */
    public function stateAction()
    {
        if ($this->getRequest()->getParam('isAjax') == 1 && $this->getRequest()->getParam('container') != ''
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
