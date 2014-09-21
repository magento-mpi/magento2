<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Model;

/**
 * Repository class for \Magento\Rma\Model\Rma
 */
class RmaRepository
{
    /**
     * rmaFactory
     *
     * @var \Magento\Rma\Model\RmaFactory
     */
    protected $rmaFactory = null;

    /**
     * Collection Factory
     *
     * @var \Magento\Rma\Model\Resource\Rma\CollectionFactory
     */
    protected $rmaCollectionFactory = null;

    /**
     * Magento\Rma\Model\Rma[]
     *
     * @var array
     */
    protected $registry = array();

   /**
    * Repository constructor
    *
    * @param RmaFactory $rmaFactory
    * @param Resource\Rma\CollectionFactory $rmaCollectionFactory
    */
    public function __construct(
        \Magento\Rma\Model\RmaFactory $rmaFactory,
        \Magento\Rma\Model\Resource\Rma\CollectionFactory $rmaCollectionFactory
    ) {
        $this->rmaFactory = $rmaFactory;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
    }

    /**
     * load entity
     *
     * @param int $id
     * @return \Magento\Rma\Model\Rma
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (!$id) {
            throw new \Magento\Framework\Exception\InputException('ID required');
        }
        if (!isset($this->registry[$id])) {
            $entity = $this->rmaFactory->create()->load($id);
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
     * @param \Magento\Rma\Model\Rma $object
     * @return \Magento\Rma\Model\RmaRepository
     */
    public function register(\Magento\Rma\Model\Rma $object)
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
     * @return \Magento\Rma\Model\Rma[]
     */
    public function find(\Magento\Framework\Service\V1\Data\SearchCriteria $criteria)
    {
        $collection = $this->rmaCollectionFactory->create();
        foreach($criteria->getFilterGroups() as $filterGroup) {
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
