<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Magento\Framework\App\State;

require_once __DIR__ . '/../../../../../app/bootstrap.php';
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, [State::PARAM_MODE => State::MODE_DEFAULT]);
$app = $bootstrap->createApplication('Magento\Tools\SampleData\Installer');
$bootstrap->run($app);
