<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    translate
 * @copyright  {copyright}
 * @license    {license_link}
 */

include('ModuleTranslations.php');

namespace Magento\Tools\Translate;

class DirectoryFilter extends FilterIterator
{
    /**
     * List of allowed extensions
     *
     * @var array
     */
    protected $_allowedExtensions;

    /**
     * @param Iterator $iterator
     * @param array $allowedExtensions
     */
    public function __construct(Iterator $iterator, array $allowedExtensions)
    {
        parent::__construct($iterator);
        $this->_allowedExtensions = $allowedExtensions;
    }


    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Check whether the current element of the iterator is acceptable
     * @link http://php.net/manual/en/filteriterator.accept.php
     * @return bool true if the current element is acceptable, otherwise false.
     */
    public function accept()
    {
        /** @var $current SPLFileInfo */
        $current = $this->current();
        return in_array($current->getExtension(), $this->_allowedExtensions);
    }

}
?>
