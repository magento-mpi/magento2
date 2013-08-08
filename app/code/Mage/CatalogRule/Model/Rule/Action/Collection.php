<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_CatalogRule_Model_Rule_Action_Collection extends Mage_Rule_Model_Action_Collection
{
    /**
     * @param Magento_Core_Model_View_Url $viewUrl
     */
    public function __construct(Magento_Core_Model_View_Url $viewUrl)
    {
        parent::__construct($viewUrl);
        $this->setType('Mage_CatalogRule_Model_Rule_Action_Collection');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $actions = parent::getNewChildSelectOptions();
        $actions = array_merge_recursive($actions, array(
            array(
                'value' => 'Mage_CatalogRule_Model_Rule_Action_Product',
                'label' => Mage::helper('Mage_CatalogInventory_Helper_Data')->__('Update the Product')
        )));
        return $actions;
    }
}
