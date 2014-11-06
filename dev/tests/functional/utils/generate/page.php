<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once dirname(__DIR__) . '/' . 'bootstrap.php';
$includePath = new \Magento\Framework\Autoload\IncludePath();
spl_autoload_register([$includePath, 'load']);
$objectManager->create('Mtf\Util\Generate\Page')->launch();
\Mtf\Util\Generate\GenerateResult::displayResults();
