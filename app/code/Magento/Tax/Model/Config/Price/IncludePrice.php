<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Config\Price;

class IncludePrice extends \Magento\Core\Model\Config\Value
{
    public function afterSave()
    {
        parent::afterSave();
        \Mage::app()->cleanCache('checkout_quote');
    }
}
