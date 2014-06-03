<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Backend;

class Filename extends \Magento\Framework\App\Config\Value
{
    /**
     * @return $this
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        $value = basename($value);
        $this->setValue($value);
        return $this;
    }
}
