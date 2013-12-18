<?php
/**
 * Session config interface
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Sesstion
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Session;

interface SaveHandlerInterface extends \Zend_Session_SaveHandler_Interface
{
    /**
     * Default session save handler
     */
    const DEFAULT_HANDLER = 'files';
}
