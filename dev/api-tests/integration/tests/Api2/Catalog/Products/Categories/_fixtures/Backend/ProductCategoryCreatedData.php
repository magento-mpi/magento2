<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require dirname(__FILE__).'/../new_category_on_new_store.php';

return array(
    'category_id' => Magento_Test_Webservice::getFixture('category')->getId()
);
