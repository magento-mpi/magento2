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
     * @var \Magento\Cms\Model\Resource\PageCriteria
     */
    protected $criteria;

    /**
     * @var \Magento\Cms\Model\PageRepository
     */
    protected $repository;

    /**
     * @param \Magento\Cms\Model\Resource\PageCriteria $criteria
     * @param \Magento\Cms\Model\PageRepository $repository
     */
    public function __construct(
        \Magento\Cms\Model\Resource\PageCriteria $criteria,
        \Magento\Cms\Model\PageRepository $repository
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
     * @return \Magento\Cms\Model\Resource\Page\Collection
     */
    public function getResultCollection()
    {
        return $this->repository->getList($this->criteria);
    }

    /**
     * Add Criteria object
     *
     * @param \Magento\Cms\Model\Resource\PageCriteria $criteria
     * @return void
     */
    public function addCriteria(\Magento\Cms\Model\Resource\PageCriteria $criteria)
    {
        $this->data[self::PART_CRITERIA_LIST]['list'][] = $criteria;
    }
}
