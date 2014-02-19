<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Block\Adminhtml\Export;

/**
 * Export filter block
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Filter
    extends \Magento\ImportExport\Block\Adminhtml\Export\Filter
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
            return $this->getUrl('adminhtml/scheduled_operation/getFilter', array(
                'entity' => $this->getOperation()->getEntity()
            ));
        } else {
            return $this->getUrl('adminhtml/scheduled_operation/getFilter');
        }
    }
}
