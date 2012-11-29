<?php
/**
 * SaaS application "entry point", requires "SaaS access point" to delegate execution to it
 *
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Run application based on invariant configuration string
 *
 * Both "SaaS access point" and this "entry point" have a convention: API consists of one and only one string argument
 * Underlying implementation may differ, in future versions of the entry point, but API should remain the same
 *
 * @param string $appConfigString
 */
return function ($appConfigString) {
    $params = array_merge($_SERVER, unserialize($appConfigString));
    require __DIR__ . '/app/bootstrap.php';
    Mage::run($params);
};
