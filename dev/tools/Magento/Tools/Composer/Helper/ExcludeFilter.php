<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Helper;

/**
 * Class for excluding folders while zipping
 */
class ExcludeFilter extends \RecursiveFilterIterator
{
    /**
     * Paths to be excluded (the path is full path not relative)
     *
     * @var array
     */
    protected $exclude;

    /**
     * ExcludeFilter Constructor
     *
     * @param  \RecursiveDirectoryIterator $iterator
     * @param array $exclude
     */
    public function __construct(\RecursiveDirectoryIterator $iterator, array $exclude)
    {
        parent::__construct($iterator);
        $this->exclude = $exclude;
    }

    /**
     * {@inheritdoc}
     */
    public function accept()
    {
        return !($this->current()->isDir() && in_array(
                str_replace(
                    '\\',
                    '/',
                    realpath($this->current()->getPathname())
                ),
                $this->exclude
            )
        );
    }

    /**
     * Getting the children of Inner Iterator
     *
     * @return \RecursiveDirectoryIterator
     */
    public function getChildren()
    {
        return new ExcludeFilter(
            $this->getInnerIterator()->getChildren(),
            $this->exclude
        );
    }
}
