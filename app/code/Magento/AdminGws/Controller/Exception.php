<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdminGws\Controller;

/**
 * Controller exception for admin area
 *
 */
class Exception extends \Magento\Framework\App\Action\Exception
{
    /**
     * @var string
     */
    protected $_defaultActionName = 'denied';
}
