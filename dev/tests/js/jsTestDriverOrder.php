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
    '/lib/web/jquery/jquery.js',
    '/lib/web/jquery/jquery-ui.js',
    '/lib/web/jquery/jquery.cookie.js',
    '/lib/web/headjs/head.load.min.js',
    '/lib/web/mage/mage.js',
    '/lib/web/mage/decorate.js',
    '/lib/web/jquery/jquery.validate.js',
    '/lib/web/jquery/jquery.metadata.js'
);
