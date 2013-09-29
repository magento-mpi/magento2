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
namespace Magento\Eav\Model\Entity\Increment;

abstract class AbstractIncrement extends \Magento\Object
    implements \Magento\Eav\Model\Entity\Increment\IncrementInterface
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
