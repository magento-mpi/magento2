<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource\Layout;

/**
 * Layout Link resource model
 */
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
