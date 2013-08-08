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
 * Newsletter problems collection
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Newsletter_Model_Resource_Grid_Collection extends Mage_Newsletter_Model_Resource_Problem_Collection
{
    /**
     * Adds queue info to grid
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract|Mage_Newsletter_Model_Resource_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addSubscriberInfo()
            ->addQueueInfo();
        return $this;
    }
}
