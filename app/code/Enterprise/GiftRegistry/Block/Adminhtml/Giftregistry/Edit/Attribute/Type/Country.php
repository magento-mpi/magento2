<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry_Edit_Attribute_Type_Country
    extends Magento_Adminhtml_Block_Widget_Form
{

    protected $_template = 'edit/type/country.phtml';

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
            ->setOptions(Mage::getSingleton('Magento_Backend_Model_Config_Source_Yesno')->toOptionArray());

        return $select->getHtml();
    }
}
