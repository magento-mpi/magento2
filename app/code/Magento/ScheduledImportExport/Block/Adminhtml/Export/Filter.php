<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export filter block
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ScheduledImportExport\Block\Adminhtml\Export;

class Filter extends \Magento\ImportExport\Block\Adminhtml\Export\Filter
{
    /**
     * Get grid url
     *
     * @param array $params
     * @return string
     */
    public function getAbsoluteGridUrl($params = array())
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
            return $this->getUrl('*/scheduled_operation/getFilter', array(
                'entity' => $this->getOperation()->getEntity()
            ));
        } else {
            return $this->getUrl('*/scheduled_operation/getFilter');
        }
    }
}
