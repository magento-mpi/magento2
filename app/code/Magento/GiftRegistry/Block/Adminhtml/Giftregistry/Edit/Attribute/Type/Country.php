<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_Type_Country
    extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * @var Magento_Backend_Model_Config_Source_Yesno
     */
    protected $sourceYesNo;

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'edit/type/country.phtml';

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Backend_Model_Config_Source_Yesno $sourceYesNo
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Backend_Model_Config_Source_Yesno $sourceYesNo,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->sourceYesNo = $sourceYesNo;
    }

    /**
     * Select element for choosing show region option
     *
     * @return string
     */
    public function getRegionShowSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Html_Select')
            ->setData(array(
                'id'    => '{{prefix}}_attribute_{{id}}_show_region',
                'class' => 'select global-scope'
            ))
            ->setName('attributes[{{prefix}}][{{id}}][show_region]')
            ->setOptions($this->sourceYesNo->toOptionArray());

        return $select->getHtml();
    }
}
