<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CurrencySymbol
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Manage currency symbols block
 *
 * @category   Magento
 * @package    Magento_CurrencySymbol
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CurrencySymbol\Block\Adminhtml\System;

class Currencysymbol extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Constructor. Initialization required variables for class instance.
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento\CurrencySymbol\System';
        $this->_controller = 'adminhtml_system_currencysymbol';
        parent::_construct();
    }

    /**
     * Custom currency symbol properties
     *
     * @var array
     */
    protected $_symbolsData = array();

    /**
     * Prepares layout
     *
     * @return \Magento\Core\Block\AbstractBlock
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Returns page header
     *
     * @return bool|string
     */
    public function getHeader()
    {
        return __('Currency Symbols');
    }

    /**
     * Returns 'Save Currency Symbol' button's HTML code
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        /** @var $block \Magento\Core\Block\AbstractBlock */
        $block = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button');
        $block->setData(array(
            'label'     => __('Save Currency Symbols'),
            'class'     => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#currency-symbols-form'),
                ),
            ),
        ));

        return $block->toHtml();
    }

    /**
     * Returns URL for save action
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }

    /**
     * Returns website id
     *
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->getRequest()->getParam('website');
    }

    /**
     * Returns store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getRequest()->getParam('store');
    }

    /**
     * Returns Custom currency symbol properties
     *
     * @return array
     */
    public function getCurrencySymbolsData()
    {
        if(!$this->_symbolsData) {
            $this->_symbolsData =  \Mage::getModel('Magento\CurrencySymbol\Model\System\Currencysymbol')
                ->getCurrencySymbolsData();
        }
        return $this->_symbolsData;
    }

    /**
     * Returns inheritance text
     *
     * @return string
     */
    public function getInheritText()
    {
        return __('Use Standard');
    }
}
