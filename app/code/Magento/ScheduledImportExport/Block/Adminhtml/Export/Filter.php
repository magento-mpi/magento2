<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\ScheduledImportExport\Block\Adminhtml\Export;

/**
 * Export filter block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Filter extends \Magento\ImportExport\Block\Adminhtml\Export\Filter
{
    /**
     * Get grid url
     *
     * @param array $params
     * @return string
     */
    public function getAbsoluteGridUrl($params = [])
    {
        return $this->getGridUrl();
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        if ($this->hasOperation()) {
            return $this->getUrl(
                'adminhtml/scheduled_operation/getFilter',
                ['entity' => $this->getOperation()->getEntity()]
            );
        } else {
            return $this->getUrl('adminhtml/scheduled_operation/getFilter');
        }
    }
}
