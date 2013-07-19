<?php
/**
 * Sales Rules resource collection model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_SalesRule_Model_Resource_Rule_Quote_Collection extends Mage_SalesRule_Model_Resource_Rule_Collection
{
    /**
     * Add websites for load
     *
     * @return Mage_SalesRule_Model_Resource_Rule_Quote_GridCollection
     */

    public function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }

}
