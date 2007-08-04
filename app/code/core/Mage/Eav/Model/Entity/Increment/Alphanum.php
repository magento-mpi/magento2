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
class Mage_Eav_Model_Entity_Increment_Alphanum
    extends Mage_Eav_Model_Entity_Increment_Abstract
{
    public function getAllowedChars()
    {
        return '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    
    public function getNextId()
    {
        $last = $this->getLastId();
        
        $chars = $this->getAllowedChars();
        
        if (strpos($last, $this->getPrefix())===0) {
            $last = substr($last, strlen($this->getPrefix()));
        }
        
        $last = str_pad((string)$last, $this->getPadLength(), $this->getPadChar(), STR_PAD_LEFT);
        
        $next = '';
        $bumpNext = false;
        for ($l=strlen($last)-1, $i=$l; $i >= 0; $i--) {
            $p = strpos($chars, $last{$i});
            if (false===$p) {
                throw Mage::exception('Mage_Eav', 'Invalid character encountered in increment ID: '.$last);
            }
            if ($bumpNext) {
                $p++;
            }
            if ($p===$l) {
                $p = 0;
                $bumpNext = true;
            }
            $next.= $chars{$p};
        }
        
        return $this->format($next);
    }
}