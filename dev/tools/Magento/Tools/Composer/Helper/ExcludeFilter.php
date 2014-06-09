<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Tools\Composer\Helper;

class ExcludeFilter extends \RecursiveFilterIterator
{
    protected $exclude;

    public function __construct(\RecursiveDirectoryIterator $iterator, array $exclude)
    {
        parent::__construct($iterator);
        $this->exclude = $exclude;
    }

    public function accept()
    {
        return !($this->current()->isDir() && in_array(realpath($this->current()->getPathname()), $this->exclude));
    }

    public function getChildren()
    {
        if (empty($this->ref)) {
            $this->ref = new \ReflectionClass($this);
        }

        return new ExcludeFilter(
            $this->getInnerIterator()->getChildren(),
            $this->exclude
        );
    }
}