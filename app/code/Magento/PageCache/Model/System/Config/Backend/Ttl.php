<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model\System\Config\Backend;

/**
 * Backend model for processing Public content cache lifetime settings
 *
 * Class Ttl
 * @package Magento\PageCache\Model\System\Config\Backend
 */
class Ttl extends \Magento\App\Config\Value
{
    /**
     * Throw exception if Ttl data is invalid or empty
     *
     * @return $this
     * @throws \Magento\Model\Exception
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if ($value < 0 || !preg_match('/^[0-9]+$/', $value)) {
            throw new \Magento\Model\Exception(
                __('Ttl value "%1" is not valid. Please use only numbers equal or greater than zero.', $value)
            );
        }
        return $this;
    }
}
