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
    public function __construct(Mage_Core_Model_View_Url $viewUrl)
    {
        parent::__construct($viewUrl);
        $this->setType('Mage_SalesRule_Model_Rule_Action_Collection');
    }

    public function getNewChildSelectOptions()
    {
        $actions = parent::getNewChildSelectOptions();
        $actions = array_merge_recursive($actions, array(
            array('value'=>'Mage_SalesRule_Model_Rule_Action_Product', 'label'=>Mage::helper('Mage_SalesRule_Helper_Data')->__('Update the Product')),
        ));
        return $actions;
    }
}
