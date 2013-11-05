<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Controller exception for admin area
 *
 */
namespace Magento\AdminGws\Controller;

class Exception extends \Magento\App\Action\Exception
{
    protected $_defaultActionName = 'denied';
}
