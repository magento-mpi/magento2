<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once dirname(__DIR__) . '/' . 'bootstrap.php';

$entryPoint = new \Magento\Framework\App\EntryPoint\EntryPoint(BP, $_SERVER);
$entryPoint->run('Mtf\Util\Generate\Factory');
\Mtf\Util\Generate\GenerateResult::displayResults();
