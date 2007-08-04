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
abstract class Mage_Eav_Model_Entity_Increment_Abstract
    extends Varien_Object
    implements Mage_Eav_Model_Entity_Increment_Interface
{
    public function format($id)
    {
        $result = $this->getPrefix();
        $result.= str_pad((string)$id, $this->getPadLength(), $this->getPadChar(), STR_PAD_LEFT);
        return $result;
    }
    
    public function frontendFormat($id)
    {
        $result = $id;
        return $result;
    }
}