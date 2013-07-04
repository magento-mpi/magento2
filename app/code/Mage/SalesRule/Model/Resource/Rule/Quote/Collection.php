<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales Rules resource collection model
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @author      Magento Core Team <core@magentocommerce.com>
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
