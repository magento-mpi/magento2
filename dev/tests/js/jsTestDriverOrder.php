<?php
/**
 * Returns an array of Javascript files that should be loaded first by JsTestDriver in the
 * order that they appear in the array when the Javascript unit tests are run.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @return array
 */
return array(
    '/lib/web/jquery/jquery-1.8.2.js',
    '/lib/web/jquery/jquery-ui-1.9.2.js',
    '/dev/tests/js/framework/requirejs-util.js',
    '/lib/web/jquery/jquery.cookie.js',
    '/lib/web/mage/mage.js',
    '/lib/web/mage/decorate.js',
    '/lib/web/jquery/jquery.validate.js',
    '/lib/web/jquery/jquery.metadata.js',
    '/lib/web/mage/translate.js',
    '/lib/web/mage/validation.js',
    '/lib/web/mage/requirejs/plugin/id-normalizer.js',
);
