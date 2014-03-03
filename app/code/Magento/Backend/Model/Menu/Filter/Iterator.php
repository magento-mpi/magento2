<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Menu\Filter;

/**
 * Menu filter iterator
 */
class Iterator extends \FilterIterator
{
    /**
     * Constructor
     *
     * @param \Iterator $iterator
     */
    public function __construct(\Iterator $iterator)
    {
        parent::__construct($iterator);
    }

    /**
     * Check whether the current element of the iterator is acceptable
     *
     * @return bool true if the current element is acceptable, otherwise false.
     */
    public function accept()
    {
        return !($this->current()->isDisabled() || !($this->current()->isAllowed()));
    }
}
