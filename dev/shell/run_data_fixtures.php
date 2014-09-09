<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

use Magento\Framework\App\State as AppState;

require __DIR__ . '/../../app/bootstrap.php';
$params = $_SERVER;
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);
/** @var \Magento\Framework\Module\Updater $updater */
$updater = $bootstrap->getObjectManager()->create('\Magento\Framework\Module\Updater');
$updater->updateData();
