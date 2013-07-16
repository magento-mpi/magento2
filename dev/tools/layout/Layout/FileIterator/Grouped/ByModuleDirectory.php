<?php
class Layout_FileIterator_Grouped_ByModuleDirectory implements IteratorAggregate
{
    protected $_innerIterator;
    protected $_layoutHelper;
    protected $_outerIterator = null;

    public function __construct(Traversable $filesIterator, Layout_Helper $layoutHelper)
    {
        $this->_innerIterator = $filesIterator;
        $this->_layoutHelper = $layoutHelper;
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
            $moduleDir = $this->_layoutHelper->getModuleDir($filePath);
            if (!isset($groups[$moduleDir])) {
                $groups[$moduleDir] = array();
            }
            $groups[$moduleDir][] = $filePath;
        }
        $this->_outerIterator = new ArrayIterator($groups);
    }
}
