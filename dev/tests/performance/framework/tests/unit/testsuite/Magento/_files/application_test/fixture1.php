<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     performance_tests
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

// Application test is exposed in global space
/** @var $applicationTestForFixtures Magento_ApplicationTest */
$applicationTestForFixtures = $GLOBALS['applicationTestForFixtures'];
$applicationTestForFixtures->addFixtureEvent('fixture1');
