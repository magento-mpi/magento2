<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Session;

/**
 * To prevent the session start in console uninstall
 */
class SessionConsole extends Generic
{
    public function __construct()
    {

    }
}
