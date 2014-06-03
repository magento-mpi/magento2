<?php
/**
 * Session config interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Session;

interface SaveHandlerInterface extends \Zend_Session_SaveHandler_Interface
{
    /**
     * Default session save handler
     */
    const DEFAULT_HANDLER = 'files';
}
