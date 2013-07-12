<?php
class Layout_FileIterator_Grouped_ByDirectory implements IteratorAggregate
{
    protected $_innerIterator;
    protected $_outerIterator = null;

    public function __construct(Traversable $filesIterator)
    {
        $this->_innerIterator = $filesIterator;
    }

    public function getIterator()
    {
        if (!$this->_outerIterator) {
            $this->_groupFilesToOuterIterator();
        }
        return $this->_outerIterator;
    }

    protected function _groupFilesToOuterIterator()
    {
        $groups = array();
        foreach ($this->_innerIterator as $filePath) {
            $dir = dirname($filePath);
            if (!isset($groups[$dir])) {
                $groups[$dir] = array();
            }
            $groups[$dir][] = $filePath;
        }
        $this->_outerIterator = new ArrayIterator($groups);
    }
}
