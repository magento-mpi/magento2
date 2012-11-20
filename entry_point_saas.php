<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

return function ($appConfigString) {
    /**
     * Invariant: application configuration string - local configuration data
     */
    $localConfigData = $appConfigString;

    require_once __DIR__ . '/app/bootstrap.php';

    $appOptions = new Mage_Core_Model_App_Options($_SERVER);
    $appRunOptions = array_merge(
        $appOptions->getRunOptions(),
        array(Mage_Core_Model_Config::OPTION_LOCAL_CONFIG_EXTRA_DATA => $localConfigData)
    );
    Mage::run($appOptions->getRunCode(), $appOptions->getRunType(), $appRunOptions);
};
