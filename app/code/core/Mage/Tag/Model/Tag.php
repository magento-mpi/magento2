<?php
class Mage_Tag_Model_Tag extends Mage_Core_Model_Abstract
{

    const STATUS_DISABLED = -1;
    const STATUS_PENDING = 0;
    const STATUS_APPROVED = 1;

    protected function _construct()
    {
        $this->_init('tag/tag');
    }

}
