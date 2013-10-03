<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Logging event changes model
 *
 * @category    Magento
 * @package     Magento_Logging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Logging\Model\Resource\Event;

class Changes extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('magento_logging_event_changes', 'id');
    }
}
