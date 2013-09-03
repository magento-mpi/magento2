<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\MagentoException
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Error of not fulfilling basic requirements necessary for the application bootstrap.
 * \Exception handling should not rely on any part of the application because it has not been initialized yet.
 */
namespace Magento;

class BootstrapException extends \Magento\MagentoException
{

}
