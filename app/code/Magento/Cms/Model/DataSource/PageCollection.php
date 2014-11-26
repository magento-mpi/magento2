<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\DataSource;

use Magento\Framework\Data\CollectionDataSourceInterface;
use Magento\Cms\Model\Resource\PageCriteria;

/**
 * CMS page collection data source
 *
 * Class PageCollection
 */
class PageCollection extends PageCriteria implements CollectionDataSourceInterface
{
    /**
     * @var \Magento\Cms\Api\PageRepositoryInterface
     */
    protected $repository;

    /**
     * @param \Magento\Cms\Api\PageRepositoryInterface $repository
     * @param string $mapper
     */
    public function __construct(
        \Magento\Cms\Api\PageRepositoryInterface $repository,
        $mapper = 'Magento\Cms\Model\Resource\PageCriteriaMapper'
    ) {
        $this->repository = $repository;
        $this->setFirstStoreFlag(true);
        parent::__construct($mapper);
    }

    /**
     * @inheritdoc
     */
    public function addFilter($name, $field, $condition = null, $type = 'public')
    {
        if ($field === 'store_id') {
            $this->addStoreFilter($condition, false);
        } else {
            parent::addFilter($name, $field, $condition, $type);
        }
    }

    /**
     * @return \Magento\Cms\Api\Data\PageCollectionInterface
     */
    public function getResultCollection()
    {
        return $this->repository->getList($this);
    }
}
