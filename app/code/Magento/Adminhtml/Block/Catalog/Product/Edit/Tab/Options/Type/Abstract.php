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
 * customers defined options
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Type_Abstract extends Magento_Adminhtml_Block_Widget
{
    protected $_name = 'abstract';

    /**
     * @var Magento_Catalog_Model_Config_Source_Product_Options_Price
     */
    protected $_optionPrice;

    /**
     * @param Magento_Catalog_Model_Config_Source_Product_Options_Price $optionPrice
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Config_Source_Product_Options_Price $optionPrice,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_optionPrice = $optionPrice;
        parent::__construct($coreData, $context, $data);
    }

    protected function _prepareLayout()
    {
        $this->setChild('option_price_type',
            $this->getLayout()->addBlock('Magento_Adminhtml_Block_Html_Select', '', $this->getNameInLayout())
                ->setData(array(
                    'id' => 'product_option_${option_id}_price_type',
                    'class' => 'select product-option-price-type'
                ))
        );

        $this->getChildBlock('option_price_type')
            ->setName('product[options][${option_id}][price_type]')
            ->setOptions($this->_optionPrice->toOptionArray());

        return parent::_prepareLayout();
    }

    /**
     * Get html of Price Type select element
     *
     * @return string
     */
    public function getPriceTypeSelectHtml()
    {
        if ($this->getCanEditPrice() === false) {
            $this->getChildBlock('option_price_type')->setExtraParams('disabled="disabled"');
        }
        return $this->getChildHtml('option_price_type');
    }

}
