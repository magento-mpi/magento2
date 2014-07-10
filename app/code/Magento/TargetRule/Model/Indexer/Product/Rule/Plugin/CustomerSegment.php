<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Model\Indexer\Product\Rule\Plugin;

class CustomerSegment extends AbstractPlugin
{
    /**
     * Invalidate target rule indexer after deleting customer segment
     *
     * @param \Magento\CustomerSegment\Model\Segment $customerSegment
     * @return \Magento\CustomerSegment\Model\Segment
     */
    public function afterDelete(\Magento\CustomerSegment\Model\Segment $customerSegment)
    {
        $this->invalidateIndexer();
        return $customerSegment;
    }

    /**
     * Invalidate target rule indexer after changing customer segment
     *
     * @param \Magento\CustomerSegment\Model\Segment $customerSegment
     * @return \Magento\CustomerSegment\Model\Segment
     */
    public function afterSave(\Magento\CustomerSegment\Model\Segment $customerSegment)
    {
        if (!$customerSegment->isObjectNew()) {
            $this->invalidateIndexer();
        }
        return $customerSegment;
    }
}
