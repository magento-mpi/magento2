<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Menu;

/**
 * Menu iterator
 */
class Iterator extends \ArrayIterator
{
    /**
     * Rewind to first element
     *
     * @return void
     */
    public function rewind()
    {
        $this->ksort();
        parent::rewind();
    }
}
