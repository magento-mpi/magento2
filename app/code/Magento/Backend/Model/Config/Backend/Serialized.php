<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Backend;

class Serialized extends \Magento\Framework\App\Config\Value
{
    /**
     * @return void
     */
    protected function _afterLoad()
    {
        if (!is_array($this->getValue())) {
            $value = $this->getValue();
            $this->setValue(empty($value) ? false : unserialize($value));
        }
    }

    /**
     * @return void
     */
    public function beforeSave()
    {
        if (is_array($this->getValue())) {
            $this->setValue(serialize($this->getValue()));
        }
    }
}
