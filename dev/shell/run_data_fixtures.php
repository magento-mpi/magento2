<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

use \Magento\Framework\App\State as AppState;

/** @var \Magento\Framework\App\Bootstrap $bootstrap */
$bootstrap = require __DIR__ . '/../../app/bootstrap.php';
$bootstrap->setIsInstalledRequirement(false);
if (!isset($_SERVER[AppState::PARAM_MODE])) {
    $bootstrap->addParams([AppState::PARAM_MODE => AppState::MODE_DEVELOPER]);
}
/** @var \Magento\Framework\Module\Updater $updater */
$updater = $bootstrap->getObjectManager()->create('\Magento\Framework\Module\Updater');
$updater->updateData();
