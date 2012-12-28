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
     * Prepare select for load
     *
     * @param Varien_Db_Select $select
     * @return string
     */
    protected function _prepareSelect(Varien_Db_Select $select)
    {
        $this->addSubscriberInfo()
            ->addQueueInfo();
        return parent::_prepareSelect($select);
    }
}
