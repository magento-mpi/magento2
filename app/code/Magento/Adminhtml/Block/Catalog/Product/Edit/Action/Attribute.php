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
class Magento_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute extends Magento_Adminhtml_Block_Widget
{

    /**
     * Adminhtml catalog product edit action attribute
     *
     * @var Magento_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute
     */
    protected $_helperActionAttribute = null;

    /**
     * @param Magento_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute $helperActionAttribute
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute $helperActionAttribute,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_helperActionAttribute = $helperActionAttribute;
        parent::__construct($coreData, $context, $data);
    }

    protected function _prepareLayout()
    {
        $this->addChild('back_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Back'),
            'onclick'   => 'setLocation(\''.$this->getUrl('*/catalog_product/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
            'class' => 'back'
        ));

        $this->addChild('reset_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'     => __('Reset'),
            'onclick'   => 'setLocation(\''.$this->getUrl('*/*/*', array('_current'=>true)).'\')'
        ));

        $this->addChild('save_button', 'Magento_Adminhtml_Block_Widget_Button', array(
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
     * @return Magento_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute
     */
    protected function _getHelper()
    {
        return $this->helper('Magento_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute');
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
        $helper = $this->_helperActionAttribute;
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
