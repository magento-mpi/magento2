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

$objectManager->create('Mtf\Util\Generate\Constraint')->launch();
\Mtf\Util\Generate\GenerateResult::displayResults();
