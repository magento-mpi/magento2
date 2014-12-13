<?php
/**
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\TargetRule\Model\Indexer\TargetRule\Plugin;

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
        $this->invalidateIndexers();
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
            $this->invalidateIndexers();
        }
        return $customerSegment;
    }
}
