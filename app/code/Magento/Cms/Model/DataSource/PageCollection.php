<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\DataSource;

use Magento\Framework\Data\AbstractCriteria;
use Magento\Framework\Data\CollectionDataSourceInterface;

/**
 * CMS page collection data source
 *
 * Class PageCollection
 */
class PageCollection extends AbstractCriteria implements CollectionDataSourceInterface
{
    /**
     * @var \Magento\Cms\Api\PageCriteriaInterface
     */
    protected $criteria;

    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    protected $repository;

    /**
     * @param \Magento\Cms\Api\PageCriteriaInterface $criteria
     * @param \Magento\Cms\Api\PageRepositoryInterface $repository
     */
    public function __construct(
        \Magento\Cms\Api\PageCriteriaInterface $criteria,
        \Magento\Cms\Api\PageRepositoryInterface $repository
    ) {
        $this->criteria = $criteria;
        $this->repository = $repository;
        $this->criteria->setFirstStoreFlag(true);
    }

    /**
     * @inheritdoc
     */
    public function addFilter($name, $field, $condition = null, $type = 'public')
    {
        if ($field === 'store_id') {
            $this->criteria->addStoreFilter($condition, false);
        } else {
            $this->criteria->addFilter($name, $field, $condition, $type);
        }
    }

    /**
     * @return \Magento\Cms\Api\Data\PageCollectionInterface
     */
    public function getResultCollection()
    {
        return $this->repository->getList($this->criteria);
    }

    /**
     * Add Criteria object
     *
     * @param \Magento\Cms\Api\PageCriteriaInterface $criteria
     * @return void
     */
    public function addCriteria(\Magento\Cms\Api\PageCriteriaInterface $criteria)
    {
        $this->data[self::PART_CRITERIA_LIST]['list'][] = $criteria;
    }
}
