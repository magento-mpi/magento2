<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Price\Plugin;

use Magento\Customer\Api\GroupRepositoryInterface;

class CustomerGroup extends AbstractPlugin
{
    /**
     * Invalidate the indexer after the group is created.
     *
     * @param GroupRepositoryInterface $subject
     * @param string                        $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreateGroup(GroupRepositoryInterface $subject, $result)
    {
        $this->invalidateIndexer();
        return $result;
    }

    /**
     * Invalidate the indexer after the group is updated.
     *
     * @param GroupRepositoryInterface $subject
     * @param string                        $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterUpdateGroup(GroupRepositoryInterface $subject, $result)
    {
        $this->invalidateIndexer();
        return $result;
    }

    /**
     * Invalidate the indexer after the group is deleted.
     *
     * @param GroupRepositoryInterface $subject
     * @param string                        $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDeleteGroup(GroupRepositoryInterface $subject, $result)
    {
        $this->invalidateIndexer();
        return $result;
    }
}
