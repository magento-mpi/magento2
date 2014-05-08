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

$objectManager->get('Magento\Framework\App\State')->setAreaCode('frontend');
$objectManager->create('Mtf\Util\Generate\Repository')->launch();
\Mtf\Util\Generate\GenerateResult::displayResults();
