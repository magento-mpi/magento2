<?php
/**
 * Application entry point
 *
 * Example - run a particular store or website:
 * --------------------------------------------
 * $extra = ['MAGE_RUN_CODE' => 'website2', 'MAGE_RUN_TYPE' => 'website'];
 * $bootstrap = new \Magento\Framework\App\Bootstrap(BP, $_SERVER, $extra);
 * $app = $bootstrap->createApplication('Magento\Framework\App\Http');
 * $bootstrap->run($app);
 * --------------------------------------------
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/app/bootstrap.php';
$bootstrap = new \Magento\Framework\App\Bootstrap(BP, $_SERVER);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('Magento\Framework\App\Http');
$bootstrap->run($app);
