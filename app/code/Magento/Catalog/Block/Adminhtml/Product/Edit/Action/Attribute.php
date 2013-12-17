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
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Edit\Action;

class Attribute extends \Magento\Backend\Block\Widget
{

    /**
     * Adminhtml catalog product edit action attribute
     *
     * @var \Magento\Catalog\Helper\Product\Edit\Action\Attribute
     */
    protected $_helperActionAttribute = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Helper\Product\Edit\Action\Attribute $helperActionAttribute
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Helper\Product\Edit\Action\Attribute $helperActionAttribute,
        array $data = array()
    ) {
        $this->_helperActionAttribute = $helperActionAttribute;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $this->addChild('back_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Back'),
            'onclick'   => 'setLocation(\''.$this->getUrl('catalog/product/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
            'class' => 'back'
        ));

        $this->addChild('reset_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Reset'),
            'onclick'   => 'setLocation(\''.$this->getUrl('catalog/*/*', array('_current'=>true)).'\')'
        ));

        $this->addChild('save_button', 'Magento\Backend\Block\Widget\Button', array(
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
     * @return \Magento\Catalog\Helper\Product\Edit\Action\Attribute
     */
    protected function _getHelper()
    {
        return $this->_helperActionAttribute;
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
        return $this->getUrl('catalog/*/validate', array('_current'=>true));
    }
}
