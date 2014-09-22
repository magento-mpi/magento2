<?php
/**
 * Application entry point
 *
 * Example - run a particular store or website:
 * --------------------------------------------
 * $extra = ['MAGE_RUN_CODE' => 'website2', 'MAGE_RUN_TYPE' => 'website'];
 * $params = array_merge($_SERVER, $extra);
 * $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params, null);
 * \/** @var \Magento\Framework\App\Http $app *\/
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
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER, null);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication('Magento\Framework\App\Http');
$bootstrap->run($app);
