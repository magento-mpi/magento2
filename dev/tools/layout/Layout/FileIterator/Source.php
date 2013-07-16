<?php
class Layout_FileIterator_Source implements IteratorAggregate
{
    protected $_layoutDirPattern;
    protected $_relativePatterns;
    protected $_iterator = null;

    public function __construct($layoutDirPattern, array $relativePatterns)
    {
        $this->_layoutDirPattern = $layoutDirPattern;
        $this->_relativePatterns = $relativePatterns;
    }

    public function getIterator()
    {
        if (!$this->_iterator) {
            $this->_iterator = new AppendIterator();
            foreach ($this->_relativePatterns as $relativePattern) {
                $iterator = new GlobIterator($this->_layoutDirPattern . '/' . $relativePattern,
                    FilesystemIterator::CURRENT_AS_PATHNAME | FilesystemIterator::UNIX_PATHS);
                $this->_iterator->append($iterator);
            }
        }
        return $this->_iterator;
    }
}
