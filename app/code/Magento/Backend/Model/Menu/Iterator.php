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
class Magento_Backend_Model_Menu_Iterator extends ArrayIterator
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
