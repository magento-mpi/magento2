<?php
/**
 * A layout update stored in database
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$layoutUpdate = new Mage_Core_Model_Layout_Update;
$layoutUpdate->setData((array(
    'handle' => 'fixture_handle',
    'xml' => '<reference name="root"><block type="Mage_Core_Block_Template" template="dummy.phtml"/></reference>',
    'sort_order' => 0,
    'store_id' => 1,
    'area' => 'frontend',
    'package' => 'test',
    'theme' => 'default',
)));
$layoutUpdate->save();
