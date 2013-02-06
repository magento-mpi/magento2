<?php
/**
 * SaaS application "entry point", requires "SaaS access point" to delegate execution to it
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Run application based on invariant configuration string
 *
 * Both "SaaS access point" and this "entry point" have a convention: API consists of one and only one string argument
 * Underlying implementation may differ, in future versions of the entry point, but API should remain the same
 *
 * @param array $appConfigArray
 */
return function ($appConfigArray) {
    require __DIR__ . '/app/bootstrap.php';

    $tenant = new Saas_Saas_Model_Tenant($appConfigArray);

    $resultArray = array(
        Mage_Core_Model_App::INIT_OPTION_DIRS => array(
            Mage_Core_Model_Dir::MEDIA => $appConfigArray['magento_dir'] . '/media/' . $tenant->getMediaDir(),
            Mage_Core_Model_Dir::VAR_DIR => $appConfigArray['magento_dir'] . '/var/' . $tenant->getVarDir(),
        ),
        Mage_Core_Model_App::INIT_OPTION_URIS => array(
            Mage_Core_Model_Dir::MEDIA => 'media/' . $tenant->getMediaDir(),
        ),
        Mage_Core_Model_Config::INIT_OPTION_EXTRA_DATA => $tenant->getConfigString(),
    );

    Mage::run(array_merge($_SERVER, $resultArray));
};
