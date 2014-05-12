<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Filter \Iterator
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Framework\Backup\Filesystem\Iterator;

class Filter extends \FilterIterator
{
    /**
     * Array that is used for filtering
     *
     * @var array
     */
    protected $_filters;

    /**
     * Constructor
     *
     * @param \Iterator $iterator
     * @param array $filters list of files to skip
     */
    public function __construct(\Iterator $iterator, array $filters)
    {
        parent::__construct($iterator);
        $this->_filters = $filters;
    }

    /**
     * Check whether the current element of the iterator is acceptable
     *
     * @return bool
     */
    public function accept()
    {
        $current = $this->current()->__toString();
        $currentFilename = $this->current()->getFilename();

        if ($currentFilename == '.' || $currentFilename == '..') {
            return false;
        }

        foreach ($this->_filters as $filter) {
            if (false !== strpos($current, $filter)) {
                return false;
            }
        }

        return true;
    }
}
