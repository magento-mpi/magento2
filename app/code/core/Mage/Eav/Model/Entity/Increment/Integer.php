<?php

/**
 * Enter description here...
 *
 * Properties:
 * - prefix
 * - pad_length
 * - pad_char
 * - last_id
 */
class Mage_Eav_Model_Entity_Increment_Integer
    extends Mage_Eav_Model_Entity_Increment_Abstract
{
    public function getNextId()
    {
        $last = $this->getLastId();
        
        if (strpos($last, $this->getPrefix())===0) {
            $last = (int)substr($last, strlen($this->getPrefix()));
        } else {
            $last = (int)$last;
        }
        
        $next = $last+1;
        
        return $this->format($next);
    }
}