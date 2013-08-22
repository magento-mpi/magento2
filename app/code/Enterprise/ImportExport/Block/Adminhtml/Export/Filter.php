<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export filter block
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Block_Adminhtml_Export_Filter extends Magento_ImportExport_Block_Adminhtml_Export_Filter
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
