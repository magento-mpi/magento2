<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Dataflow batch collection
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Resource_Batch_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Init model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Dataflow_Model_Batch', 'Mage_Dataflow_Model_Resource_Batch');
    }

    /**
     * Add expire filter (for abandoned batches)
     *
     */
    public function addExpireFilter()
    {
        $date = Mage::getSingleton('Mage_Core_Model_Date');
        /* @var $date Mage_Core_Model_Date */
        $lifetime = Mage_Dataflow_Model_Batch::LIFETIME;
        $expire   = $date->gmtDate(null, $date->timestamp() - $lifetime);

        $this->getSelect()->where('created_at < ?', $expire);
    }
}
