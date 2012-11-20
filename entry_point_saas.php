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
     * Invariant: application configuration string - extra base configuration data
     */
    $baseConfigData = $appConfigString;

    require_once __DIR__ . '/app/bootstrap.php';

    $appOptions = new Mage_Core_Model_App_Options($_SERVER);
    $appRunOptions = array_merge(
        $appOptions->getRunOptions(),
        array(Mage_Core_Model_Config::OPTION_BASE_CONFIG_EXTRA_DATA => $baseConfigData)
    );
    Mage::run($appOptions->getRunCode(), $appOptions->getRunType(), $appRunOptions);
};
