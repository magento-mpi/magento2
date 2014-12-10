<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
