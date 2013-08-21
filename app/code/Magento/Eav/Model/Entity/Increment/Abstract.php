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
abstract class Magento_Eav_Model_Entity_Increment_Abstract extends Magento_Object
    implements Magento_Eav_Model_Entity_Increment_Interface
{
    public function getPadLength()
    {
        $padLength = $this->getData('pad_length');
        if (empty($padLength)) {
            $padLength = 8;
        }
        return $padLength;
    }

    public function getPadChar()
    {
        $padChar = $this->getData('pad_char');
        if (empty($padChar)) {
            $padChar = '0';
        }
        return $padChar;
    }

    public function format($id)
    {
        $result = $this->getPrefix();
        $result.= str_pad((string)$id, $this->getPadLength(), $this->getPadChar(), STR_PAD_LEFT);
        return $result;
    }

    public function frontendFormat($id)
    {
        return $id;
    }
}
