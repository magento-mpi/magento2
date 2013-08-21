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
 * Operation resource model
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Model_Resource_Scheduled_Operation extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize resource operation model
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_scheduled_operations', 'id');

        $this->_useIsObjectNew = true;
    }
}
