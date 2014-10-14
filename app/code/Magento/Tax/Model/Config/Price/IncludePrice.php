<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Config\Price;

class IncludePrice extends \Magento\Framework\App\Config\Value
{
    /**
     * @return void
     */
    public function afterSave()
    {
        parent::afterSave();
        $this->_cacheManager->clean(array('checkout_quote'));
    }
}
