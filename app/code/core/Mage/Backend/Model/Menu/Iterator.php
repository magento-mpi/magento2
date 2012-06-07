<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Menu_Iterator extends ArrayIterator
{
    /**
     * Iterate to next item in menu
     */
    public function next()
    {
        parent::next();
        if ($this->valid() && ($this->current()->isDisabled() || !$this->current()->isAllowed())) {
            $this->next();
        }
    }

    /**
     * Rewind to first element
     */
    public function rewind()
    {
        $this->ksort();
        parent::rewind();
        if ($this->valid() && (current($this)->isDisabled() || !(current($this)->isAllowed()))) {
            $this->next();
        }
    }
}
