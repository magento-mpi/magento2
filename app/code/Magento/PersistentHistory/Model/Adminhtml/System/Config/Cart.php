<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PersistentHistory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enterprise Persistent System Config Shopping Cart option backend model
 *
 */
namespace Magento\PersistentHistory\Model\Adminhtml\System\Config;

class Cart extends \Magento\Core\Model\Config\Value
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magento_persistenthistory_options_shopping_cart';
}
