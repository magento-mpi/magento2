<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Controller;

/**
 * Controller exception for admin area
 *
 */
class Exception extends \Magento\App\Action\Exception
{
    /**
     * @var string
     */
    protected $_defaultActionName = 'denied';
}
