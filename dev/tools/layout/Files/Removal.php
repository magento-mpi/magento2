<?php
class Files_Removal
{
    protected $_iterator = null;

    public function __construct(Traversable $fileIterator)
    {
        $this->_iterator = $fileIterator;
    }

    public function run()
    {
        foreach ($this->_iterator as $file) {
            unlink($file);
        }
    }
}
