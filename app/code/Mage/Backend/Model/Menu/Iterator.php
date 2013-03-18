<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Menu iterator
 */
class Mage_Backend_Model_Menu_Iterator extends ArrayIterator
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
