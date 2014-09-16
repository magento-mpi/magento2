<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Model\Rma;

use Magento\Rma\Model\Resource\Rma;

class Repository
{
    /**
     * @var \Magento\Rma\Model\RmaFactory
     */
    private $rmaFactory;

    /**
     * \Magento\Rma\Model\Rma[]
     *
     * @var array
     */
    private $registry = [];

    /**
     * @var Rma\CollectionFactory
     */
    private $rmaCollectionFactory;

    /**
     * @param \Magento\Rma\Model\RmaFactory $rmaFactory
     * @param Rma\CollectionFactory $rmaCollectionFactory
     */
    public function __construct(
        \Magento\Rma\Model\RmaFactory $rmaFactory,
        Rma\CollectionFactory $rmaCollectionFactory
    ) {
        $this->rmaFactory = $rmaFactory;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
    }

    /**
     * Returns instance of Rma by id
     *
     * @param $id
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
            $rmaModel = $this->rmaFactory->create();
            $rmaModel->load($id);
            if (!$rmaModel->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException('Requested entity doesn\'t exist');
            }
            $this->register($rmaModel);
        }

        return $this->registry[$id];
    }

    /**
     * Register entity
     *
     * @param \Magento\Rma\Model\Rma $object
     * @return Repository
     */
    public function register(\Magento\Rma\Model\Rma $object)
    {
        if ($object->getId() && !isset($this->registry[$object->getId()])) {
            $this->registry[$object->getId()] = $object;
        }
        return $this;
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria  $criteria
     * @return Transaction[]
     */
    public function find(\Magento\Framework\Service\V1\Data\SearchCriteria $criteria)
    {
        /** @var Rma\Collection $collection */
        $collection = $this->rmaCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        foreach ($collection as $object) {
            $this->register($object);
        }
        $objectIds = $collection->getAllIds();
        return array_intersect_key($this->registry, array_flip($objectIds));
    }
}
