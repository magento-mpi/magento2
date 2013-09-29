<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Config Cookie Restriction mode backend
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Backend;

class Cookie extends \Magento\Core\Model\Config\Value
{
    protected $_eventPrefix = 'adminhtml_system_config_backend_cookie';
}
