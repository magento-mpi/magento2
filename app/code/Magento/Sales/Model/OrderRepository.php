<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Sales\Model;

class OrderRepository
{
    /**
     * orderFactory
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory = null;

    /**
     * Collection Factory
     *
     * @var \Magento\Sales\Model\Resource\Order\CollectionFactory
     */
    protected $orderCollectionFactory = null;

    /**
     * Magento\Sales\Model\Order[]
     *
     * @var array
     */
    protected $registry = array();

    /**
     * Repository constructor
     *
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Resource\Order\CollectionFactory $orderCollectionFactory
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Resource\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->orderFactory = $orderFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * load entity
     *
     * @param int $id
     * @return \Magento\Sales\Model\Order
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (!$id) {
            throw new \Magento\Framework\Exception\InputException('ID required');
        }
        if (!isset($this->registry[$id])) {
            $entity = $this->orderFactory->create()->load($id);
            if (!$entity->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException('Requested entity doesn\'t exist');
            }
            $this->registry[$id] = $entity;
        }
        return $this->registry[$id];
    }

    /**
     * Register entity
     *
     * @param \Magento\Sales\Model\Order $object
     * @return \Magento\Sales\Model\OrderRepository
     */
    public function register(\Magento\Sales\Model\Order $object)
    {
        if ($object->getId() && !isset($this->registry[$object->getId()])) {
            $object->load($object->getId());
            $this->registry[$object->getId()] = $object;
        }
        return $this;
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria  $criteria
     * @return \Magento\Sales\Model\Order[]
     */
    public function find(\Magento\Framework\Service\V1\Data\SearchCriteria $criteria)
    {
        $collection = $this->orderCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        foreach ($collection as $object) {
            $this->register($object);
        }
        $objectIds = $collection->getAllIds();
        return array_intersect_key($this->registry, array_flip($objectIds));
    }
}
