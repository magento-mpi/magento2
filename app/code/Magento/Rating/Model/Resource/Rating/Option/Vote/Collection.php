<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating votes collection
 *
 * @category    Magento
 * @package     Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rating_Model_Resource_Rating_Option_Vote_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Store list manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Rating_Model_Resource_Rating_Option_CollectionFactory
     */
    protected $_ratingCollectionF;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Rating_Model_Resource_Rating_Option_CollectionFactory $ratingCollectionF
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Rating_Model_Resource_Rating_Option_CollectionFactory $ratingCollectionF,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_ratingCollectionF = $ratingCollectionF;
        parent::__construct($eventManager, $fetchStrategy, $resource);
    }

    /**
     * Define model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Rating_Model_Rating_Option_Vote', 'Magento_Rating_Model_Resource_Rating_Option_Vote');
    }

    /**
     * Set review filter
     *
     * @param int $reviewId
     * @return Magento_Rating_Model_Resource_Rating_Option_Vote_Collection
     */
    public function setReviewFilter($reviewId)
    {
        $this->getSelect()
            ->where("main_table.review_id = ?", $reviewId);
        return $this;
    }

    /**
     * Set EntityPk filter
     *
     * @param int $entityId
     * @return Magento_Rating_Model_Resource_Rating_Option_Vote_Collection
     */
    public function setEntityPkFilter($entityId)
    {
        $this->getSelect()
            ->where("entity_pk_value = ?", $entityId);
        return $this;
    }

    /**
     * Set store filter
     *
     * @param int $storeId
     * @return Magento_Rating_Model_Resource_Rating_Option_Vote_Collection
     */
    public function setStoreFilter($storeId)
    {
        if ($this->_storeManager->isSingleStoreMode()) {
            return $this;
        }
        $this->getSelect()
            ->join(array('rstore'=>$this->getTable('review_store')),
                $this->getConnection()->quoteInto(
                    'main_table.review_id=rstore.review_id AND rstore.store_id=?',
                    (int)$storeId),
            array());
        return $this;
    }

    /**
     * Add rating info to select
     *
     * @param int $storeId
     * @return Magento_Rating_Model_Resource_Rating_Option_Vote_Collection
     */
    public function addRatingInfo($storeId=null)
    {
        $adapter=$this->getConnection();
        $ratingCodeCond = $adapter->getIfNullSql('title.value', 'rating.rating_code');
        $this->getSelect()
            ->join(
                array('rating'    => $this->getTable('rating')),
                'rating.rating_id = main_table.rating_id',
                array('rating_code'))
            ->joinLeft(
                array('title' => $this->getTable('rating_title')),
                $adapter->quoteInto('main_table.rating_id=title.rating_id AND title.store_id = ?',
                    (int)$this->_storeManager->getStore()->getId()),
                array('rating_code' => $ratingCodeCond));
        if (!$this->_storeManager->isSingleStoreMode()) {
            if ($storeId == null) {
                $storeId = $this->_storeManager->getStore()->getId();
            }

            if (is_array($storeId)) {
                $condition = $adapter->prepareSqlCondition('store.store_id', array(
                    'in' => $storeId
                ));
            } else {
                $condition = $adapter->quoteInto('store.store_id = ?', $storeId);
            }

            $this->getSelect()->join(
                array('store' => $this->getTable('rating_store')),
                'main_table.rating_id = store.rating_id AND ' . $condition
            );
        }
        $adapter->fetchAll($this->getSelect());
        return $this;
    }

    /**
     * Add option info to select
     *
     * @return Magento_Rating_Model_Resource_Rating_Option_Vote_Collection
     */
    public function addOptionInfo()
    {
        $this->getSelect()
            ->join(array('rating_option' => $this->getTable('rating_option')),
                'main_table.option_id = rating_option.option_id');
        return $this;
    }

    /**
     * Add rating options
     *
     * @return Magento_Rating_Model_Resource_Rating_Option_Vote_Collection
     */
    public function addRatingOptions()
    {
        if (!$this->getSize()) {
            return $this;
        }
        foreach ($this->getItems() as $item) {
            /** @var Magento_Rating_Model_Resource_Rating_Option_Collection $options */
            $options = $this->_ratingCollectionF->create();
            $options->addRatingFilter($item->getRatingId())->load();

            if ($item->getRatingId()) {
                $item->setRatingOptions($options);
            } else {
                return $this;
            }
        }
        return $this;
    }
}
