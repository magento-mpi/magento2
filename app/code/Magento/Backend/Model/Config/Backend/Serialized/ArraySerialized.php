<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend for serialized array data
 *
 */
namespace Magento\Backend\Model\Config\Backend\Serialized;

class ArraySerialized extends \Magento\Backend\Model\Config\Backend\Serialized
{
    /**
     * Unset array element with '__empty' key
     *
     * @return void
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
        }
        $this->setValue($value);
        parent::_beforeSave();
    }
}
