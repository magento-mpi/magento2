<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Exception
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Error of not fulfilling basic requirements necessary for the application bootstrap.
 * Exception handling should not rely on any part of the application because it has not been initialized yet.
 */
class Magento_BootstrapException extends Magento_Exception
{

}
