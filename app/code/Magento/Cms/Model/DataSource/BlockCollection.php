<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\DataSource;

use Magento\Framework\Data\CollectionDataSourceInterface;
use Magento\Cms\Model\Resource\BlockCriteria;
use Magento\Cms\Api\BlockRepositoryInterface;

/**
 * CMS block collection data source
 *
 * Class BlockCollection
 */
class BlockCollection extends BlockCriteria implements CollectionDataSourceInterface
{
    /**
     * @var BlockRepositoryInterface
     */
    protected $repository;

    /**
     * @param BlockRepositoryInterface $repository
     * @param string $mapper
     */
    public function __construct(
        BlockRepositoryInterface $repository,
        $mapper = 'Magento\Cms\Model\Resource\BlockCriteriaMapper'
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
     * @return \Magento\Cms\Api\Data\BlockCollectionInterface
     */
    public function getResultCollection()
    {
        return $this->repository->getList($this);
    }
}
