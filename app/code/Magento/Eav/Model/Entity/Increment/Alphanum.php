<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enter description here...
 *
 * Properties:
 * - prefix
 * - pad_length
 * - pad_char
 * - last_id
 */
class Magento_Eav_Model_Entity_Increment_Alphanum extends Magento_Eav_Model_Entity_Increment_Abstract
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
        $lchars = strlen($chars);
        $lid = strlen($lastId)-1;

        for ($i = $lid; $i >= 0; $i--) {
            $p = strpos($chars, $lastId{$i});
            if (false===$p) {
                throw Mage::exception('Magento_Eav', __('Invalid character encountered in increment ID: %1', $lastId));
            }
            if ($bumpNextChar) {
                $p++;
                $bumpNextChar = false;
            }
            if ($p===$lchars) {
                $p = 0;
                $bumpNextChar = true;
            }
            $nextId = $chars{$p}.$nextId;
        }

        return $this->format($nextId);
    }
}
