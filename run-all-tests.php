#!/usr/bin/env php
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
chdir(__DIR__);
foreach (glob('testsuite/themes/*/*') as $testSuite)
{
    `phpunit  $testSuite`;
}