<?php
/**
 * Sales Rules resource collection model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_SalesRule_Model_Resource_Rule_Quote_Collection extends Magento_SalesRule_Model_Resource_Rule_Collection
{
    /**
     * Add websites for load
     *
     * @return Magento_SalesRule_Model_Resource_Rule_Quote_GridCollection
     */

    public function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }

}
