<?php
/**
 * {license_notice}
 *
 * @category    Social
 * @package     Social_Facebook
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Social_Facebook_Block_Adminhtml_Select extends Mage_Core_Block_Html_Select
{
    protected $_options = array();

    public function setInputName($value)
    {
        return $this->setName($value);
    }

    public function _toHtml()
    {
        $yesnoSource = Mage::getModel('Mage_Backend_Model_Config_Source_Yesno')->toOptionArray();

        foreach ($yesnoSource as $action) {
            $this->addOption($action['value'], $action['label']);
        }

        return parent::_toHtml();
    }

}
