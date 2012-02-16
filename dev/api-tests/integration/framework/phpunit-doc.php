<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * PHPUnit xml constants document
 *
 * Don't require this file
 * This file described constants in the config file phpunit.xml
 */

/**
 * Define custom HTTP host
 */
define('TESTS_HTTP_HOST', '');

/**
 * Define admin username
 */
define('TESTS_ADMIN_USERNAME', '');

/**
 * Define admin password
 */
define('TESTS_ADMIN_PASSWORD', '');

/**
 * Define customer email
 */
define('TESTS_CUSTOMER_EMAIL', '');

/**
 * Define customer password
 */
define('TESTS_CUSTOMER_PASSWORD', '');

/**
 * Define webservice type
 *
 * SOAPV2, SOAPV1, XMLRPC
 */
define('TESTS_WEBSERVICE_TYPE', '');

/**
 * Define transaction usage
 *
 * ON, OFF
 */
define('TESTS_FIXTURE_TRANSACTION', '');

/**
 * Define webservice URL
 */
define('TESTS_WEBSERVICE_URL', '');

/**
 * Define webservice API user
 */
define('TESTS_WEBSERVICE_USER', '');

/**
 * Define webservice API key
 */
define('TESTS_WEBSERVICE_APIKEY', '');

/**
 * Define test DB vendor
 *
 * MYSQL (default), ORACLE, MSSQL
 */
define('TESTS_DB_VENDOR', '');

/**
 * Define CSV Profiler Output file
 */
define('TESTS_PROFILER_FILE', '');

/**
 * Define Bamboo compatible CSV Profiler Output file name
 */
define('TESTS_BAMBOO_PROFILER_FILE', '');

/**
 * Define Metrics for Bamboo Profiler Output in PHP file that returns array
 */
define('TESTS_BAMBOO_PROFILER_METRICS_FILE', '');

/**
 * Define enable show content on get invalid response
 */
define('TESTS_WEBSERVICE_SHOW_INVALID_RESPONSE', '');

/**
 * Define test oAuth consumer
 */
define('TESTS_OAUTH_CONSUMER', '');
