<?php
/**
 * Application entry point
 *
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

require __DIR__ . '/app/bootstrap.php';
Mage::run($_SERVER);

/**
 * Example - run a particular store or website:
 *
 * $params = $_SERVER;
 * $params['MAGE_RUN_CODE'] = 'website2';
 * $params['MAGE_RUN_TYPE'] = 'website';
 * Mage::run($params)
 */
