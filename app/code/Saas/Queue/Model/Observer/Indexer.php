<?php
/**
 * Saas queue indexer observer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Saas queue indexer observer
 *
 * @category    Saas
 * @package     Saas_Queue
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Queue_Model_Observer_Indexer extends Saas_Queue_Model_ObserverAbstract
{
    /**
     * Instance of indexer model
     *
     * @var Mage_Index_Model_Indexer
     */
    protected $_indexer;

    /**
     * Basic class initialization
     *
     * @param Mage_Index_Model_Indexer $indexer
     */
    public function __construct(Mage_Index_Model_Indexer $indexer)
    {
        $this->_indexer = $indexer;
    }

    /**
     * {@inheritdoc}
     */
    public function useInEmailNotification()
    {
        return false;
    }

    /**
     * Reindex all processes
     *
     * @param  Varien_Event_Observer $observer
     * @return Saas_Queue_Model_Observer_Indexer
     */
    public function processReindexAll(Varien_Event_Observer $observer)
    {
        $this->_indexer->reindexAll();
        return $this;
    }

    /**
     * Reindex only processes that are invalidated
     *
     * @param  Varien_Event_Observer $observer
     * @return Saas_Queue_Model_Observer_Indexer
     */
    public function processReindexRequired(Varien_Event_Observer $observer)
    {
        $this->_indexer->reindexRequired();
        return $this;
    }
}
