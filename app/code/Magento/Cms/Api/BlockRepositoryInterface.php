<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Api;

/**
 * Interface BlockRepositoryInterface
 */
interface BlockRepositoryInterface
{
    /**
     * Save Block data
     *
     * @param \Magento\Cms\Api\Data\BlockInterface $block
     * @return \Magento\Cms\Api\Data\BlockInterface
     */
    public function save(\Magento\Cms\Api\Data\BlockInterface $block);

    /**
     * Load Block data by given Block Identity
     *
     * @param string $blockId
     * @return \Magento\Cms\Api\Data\BlockInterface
     */
    public function get($blockId);

    /**
     * Load Block data collection by given search criteria
     *
     * @param \Magento\Cms\Api\BlockCriteriaInterface $criteria
     * @return \Magento\Cms\Api\Data\BlockCollectionInterface
     */
    public function getList(\Magento\Cms\Api\BlockCriteriaInterface $criteria);

    /**
     * Delete Block
     *
     * @param \Magento\Cms\Api\Data\BlockInterface $block
     * @return bool
     */
    public function delete(\Magento\Cms\Api\Data\BlockInterface $block);

    /**
     * Delete Block by given Block Identity
     *
     * @param string $blockId
     * @return bool
     */
    public function deleteById($blockId);
}
