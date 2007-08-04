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
        $lastId = $this->getLastId();
        
        if (strpos($lastId, $this->getPrefix())===0) {
            $lastId = substr($lastId, strlen($this->getPrefix()));
        }
        
        $lastId = str_pad((string)$lastId, $this->getPadLength(), $this->getPadChar(), STR_PAD_LEFT);
        
        $nextId = '';
        $bumpNextChar = true;
        $chars = $this->getAllowedChars();
        $l = strlen($lastId)-1;
        
        for ($i = $l; $i >= 0; $i--) {
            $p = strpos($chars, $lastId{$i});
            if (false===$p) {
                throw Mage::exception('Mage_Eav', 'Invalid character encountered in increment ID: '.$lastId);
            }
            if ($bumpNextChar) {
                $p++;
                $bumpNextChar = false;
            }
            if ($p===$l) {
                $p = 0;
                $bumpNextChar = true;
            }
            $nextId = $chars{$p}.$nextId;
        }
        
        return $this->format($nextId);
    }
}