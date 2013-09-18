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
namespace Magento\Rating\Model\Resource\Rating\Option\Vote;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Application instance
     *
     * @var \Magento\Core\Model\App
     */
    protected $_app;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null,
        $data = array()
    ) {
        $this->_app = isset($data['app']) ? $data['app'] : \Mage::app();

        if (!($this->_app instanceof \Magento\Core\Model\App)) {
            throw new \InvalidArgumentException('Required app object is invalid');
        }
        parent::__construct($eventManager, $fetchStrategy, $resource);
    }

    /**
     * Define model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Rating\Model\Rating\Option\Vote', 'Magento\Rating\Model\Resource\Rating\Option\Vote');
    }

    /**
     * Set review filter
     *
     * @param int $reviewId
     * @return \Magento\Rating\Model\Resource\Rating\Option\Vote\Collection
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
     * @return \Magento\Rating\Model\Resource\Rating\Option\Vote\Collection
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
     * @return \Magento\Rating\Model\Resource\Rating\Option\Vote\Collection
     */
    public function setStoreFilter($storeId)
    {
        if ($this->_app->isSingleStoreMode()) {
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
     * @return \Magento\Rating\Model\Resource\Rating\Option\Vote\Collection
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
                    (int)\Mage::app()->getStore()->getId()),
                array('rating_code' => $ratingCodeCond));
        if (!$this->_app->isSingleStoreMode()) {
            if ($storeId == null) {
                $storeId = \Mage::app()->getStore()->getId();
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
     * @return \Magento\Rating\Model\Resource\Rating\Option\Vote\Collection
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
     * @return \Magento\Rating\Model\Resource\Rating\Option\Vote\Collection
     */
    public function addRatingOptions()
    {
        if (!$this->getSize()) {
            return $this;
        }
        foreach ($this->getItems() as $item) {
            $options = \Mage::getModel('Magento\Rating\Model\Rating\Option')
                    ->getResourceCollection()
                    ->addRatingFilter($item->getRatingId())
                    ->load();

            if ($item->getRatingId()) {
                $item->setRatingOptions($options);
            } else {
                return;
            }
        }
        return $this;
    }
}
