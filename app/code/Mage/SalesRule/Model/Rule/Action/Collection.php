<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_SalesRule_Model_Rule_Action_Collection extends Mage_Rule_Model_Action_Collection
{
    /**
     * @param Mage_Core_Model_View_Url $viewUrl
     * @param array $data
     */
    public function __construct(Mage_Core_Model_View_Url $viewUrl, array $data = array())
    {
        parent::__construct($viewUrl, $data);
        $this->setType('Mage_SalesRule_Model_Rule_Action_Collection');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $actions = parent::getNewChildSelectOptions();
        $actions = array_merge_recursive($actions, array(array(
            'value' => 'Mage_SalesRule_Model_Rule_Action_Product',
            'label' => Mage::helper('Mage_SalesRule_Helper_Data')->__('Update the Product'))
        ));
        return $actions;
    }
}
