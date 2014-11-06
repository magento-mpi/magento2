<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

use Magento\Framework\App\State as AppState;
use Magento\Framework\Shell\ComplexParameter;

require __DIR__ . '/../../app/bootstrap.php';
$bootstrapParam = new ComplexParameter('bootstrap');
$params = $bootstrapParam->mergeFromArgv($_SERVER, $_SERVER);
if (!isset($params[AppState::PARAM_MODE])) {
    $params[AppState::PARAM_MODE] = AppState::MODE_DEVELOPER;
}
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
/** @var \Magento\Framework\Module\Updater $updater */
$updater = $bootstrap->getObjectManager()->create('\Magento\Framework\Module\Updater');
$updater->updateData();
