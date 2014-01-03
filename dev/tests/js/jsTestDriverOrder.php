<?php
/**
 * Returns an array of Javascript files that should be loaded first by JsTestDriver in the
 * order that they appear in the array when the Javascript unit tests are run.
 *
 * {license_notice}
 *
 * @category    tests
 * @package     js
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @return array
 */
return array(
    '/pub/lib/jquery/jquery.js',
    '/pub/lib/jquery/jquery-ui.js',
    '/pub/lib/jquery/jquery.cookie.js',
    '/pub/lib/headjs/head.load.min.js',
    '/pub/lib/mage/mage.js',
    '/pub/lib/mage/decorate.js',
    '/pub/lib/jquery/jquery.validate.js',
    '/pub/lib/jquery/jquery.metadata.js'
);
