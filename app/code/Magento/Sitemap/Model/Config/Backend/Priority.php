<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Sitemap\Model\Config\Backend;

class Priority extends \Magento\Core\Model\Config\Value
{

    protected function _beforeSave()
    {
        $value     = $this->getValue();
            if ($value < 0 || $value > 1) {
                throw new \Exception(__('The priority must be between 0 and 1.'));
            } elseif (($value == 0) && !($value === '0' || $value === '0.0')) {
                throw new \Exception(__('The priority must be between 0 and 1.'));
            }
        return $this;
    }

}
