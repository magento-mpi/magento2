<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
