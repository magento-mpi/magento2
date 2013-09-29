<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout Link resource model
 */
namespace Magento\Core\Model\Resource\Layout;

class Link extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('core_layout_link', 'layout_link_id');
    }
}
