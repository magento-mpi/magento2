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
    $appConfig = unserialize($appConfigString);

    require_once __DIR__ . '/app/bootstrap.php';

    $appOptions = new Mage_Core_Model_App_Options($_SERVER);
    $appRunOptions = array_merge(
        $appOptions->getRunOptions(),
        array(Mage_Core_Model_Config::OPTION_LOCAL_CONFIG_EXTRA_DATA => $appConfig['base_config']),
        $appConfig['options']
    );
    Mage::run($appOptions->getRunCode(), $appOptions->getRunType(), $appRunOptions);
};
