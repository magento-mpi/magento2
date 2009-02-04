<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Eav
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Entity attribute option resource model
 *
 * @category   Mage
 * @package    Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Mysql4_Entity_Attribute_Option extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('eav/attribute_option', 'option_id');
    }

    /**
     * Add Join with option value for collection select
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param Zend_Db_Expr $valueExpr
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Option
     */
    public function addOptionValueToCollection($collection, $attribute, $valueExpr) {
        $attributeCode  = $attribute->getAttributeCode();
        $optionTable1   = $attributeCode . '_option_value_t1';
        $optionTable2   = $attributeCode . '_option_value_t2';

        $collection->getSelect()
            ->joinLeft(
                array($optionTable1 => $this->getTable('eav/attribute_option_value')),
                "`{$optionTable1}`.`option_id`={$valueExpr}"
                . " AND `{$optionTable1}`.`store_id`='0'",
                array())
            ->joinLeft(
                array($optionTable2 => $this->getTable('eav/attribute_option_value')),
                "`{$optionTable2}`.`option_id`={$valueExpr}"
                . " AND `{$optionTable1}`.`store_id`='{$collection->getStoreId()}'",
                array($attributeCode => "IFNULL(`{$optionTable2}`.`value`, `{$optionTable1}`.`value`)")
            );

        return $this;
    }

}
