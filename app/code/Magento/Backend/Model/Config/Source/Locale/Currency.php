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
 * Locale currency source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Source\Locale;

class Currency implements \Magento\Core\Model\Option\ArrayInterface
{
    protected $_option;
    
    public function toOptionArray()
    {
        return \Mage::app()->getLocale()->getOptionCurrencies();
    }
}
