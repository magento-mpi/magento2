<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require '_fixture/Catalog/Category/category_on_new_store.php';
return array(
    'category_id' => Magento_Test_TestCase_ApiAbstract::getFixture('category')->getId()
);
