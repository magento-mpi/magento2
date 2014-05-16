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

class IncludePrice extends \Magento\Framework\App\Config\Value
{
    /**
     * @return void
     */
    public function _afterSave()
    {
        parent::_afterSave();
        $this->_cacheManager->clean(array('checkout_quote'));
    }
}
