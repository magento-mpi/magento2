<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

use Magento\Framework\App\Bootstrap;
use \Magento\Framework\App\State as AppState;

require __DIR__ . '/../../app/bootstrap.php';
$extra = [];
if (!isset($_SERVER[AppState::PARAM_MODE])) {
    $extra[AppState::PARAM_MODE] = AppState::MODE_DEVELOPER;
}
$bootstrap = new Bootstrap(BP, $_SERVER, $extra);
$bootstrap->setIsInstalledRequirement(false);

/** @var \Magento\Framework\Module\Updater $updater */
$updater = $bootstrap->getObjectManager()->create('\Magento\Framework\Module\Updater');
$updater->updateData();
