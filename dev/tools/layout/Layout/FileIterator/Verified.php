<?php
class Layout_FileIterator_Verified extends FilterIterator
{
    public function accept()
    {
        $filePath = $this->getInnerIterator()->current();
        return strpos(file_get_contents($filePath), '<layout') !== false;
    }
}
