<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Model\Resource;

use Magento\Framework\Data\AbstractCriteria;

/**
 * Class PageCriteria
 */
class PageCriteria extends AbstractCriteria implements \Magento\Cms\Api\PageCriteriaInterface
{
    /**
     * @param string $mapper
     */
    public function __construct($mapper = '')
    {
        $this->mapperInterfaceName = $mapper ?: 'Magento\Cms\Model\Resource\PageCriteriaMapper';
    }

    /**
     * @inheritdoc
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->data['first_store_flag'] = $flag;
    }

    /**
     * @inheritdoc
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->data['store_filter'] = [$store, $withAdmin];
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
