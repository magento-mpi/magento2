<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Menu iterator
 */
namespace Magento\Backend\Model\Menu;

class Iterator extends \ArrayIterator
{
    /**
     * Rewind to first element
     */
    public function rewind()
    {
        $this->ksort();
        parent::rewind();
    }
}
