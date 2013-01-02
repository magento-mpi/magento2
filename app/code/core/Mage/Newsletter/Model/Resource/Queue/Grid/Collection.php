<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Newsletter queue data grid collection
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_Model_Resource_Queue_Grid_Collection
    extends Mage_Newsletter_Model_Resource_Queue_Collection
{
    /**
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|Mage_Newsletter_Model_Resource_Queue_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addSubscribersInfo();
        return $this;
    }
}