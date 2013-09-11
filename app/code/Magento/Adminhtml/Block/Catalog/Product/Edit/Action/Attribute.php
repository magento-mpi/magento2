<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml catalog product action attribute update
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Action;

class Attribute extends \Magento\Adminhtml\Block\Widget
{

    protected function _prepareLayout()
    {
        $this->addChild('back_button', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Back'),
            'onclick'   => 'setLocation(\''.$this->getUrl('*/catalog_product/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
            'class' => 'back'
        ));

        $this->addChild('reset_button', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Reset'),
            'onclick'   => 'setLocation(\''.$this->getUrl('*/*/*', array('_current'=>true)).'\')'
        ));

        $this->addChild('save_button', '\Magento\Adminhtml\Block\Widget\Button', array(
            'label'     => __('Save'),
            'class'     => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#attributes-edit-form'),
                ),
            ),
        ));
    }

    /**
     * Retrieve selected products for update
     *
     * @return unknown
     */
    public function getProducts()
    {
        return $this->_getHelper()->getProducts();
    }

    /**
     * Retrieve block attributes update helper
     *
     * @return \Magento\Adminhtml\Helper\Catalog\Product\Edit\Action\Attribute
     */
    protected function _getHelper()
    {
        return $this->helper('\Magento\Adminhtml\Helper\Catalog\Product\Edit\Action\Attribute');
    }

    /**
     * Retrieve back button html code
     *
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * Retrieve cancel button html code
     *
     * @return string
     */
     public function getCancelButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Retrieve save button html code
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Get save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        $helper = \Mage::helper('Magento\Adminhtml\Helper\Catalog\Product\Edit\Action\Attribute');
        return $this->getUrl(
            '*/*/save',
            array(
                'store' => $helper->getSelectedStoreId()
            )
        );
    }

    /**
     * Get validation url
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }
}
