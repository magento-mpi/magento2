<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
?>
<?php return array(
    array (
        'type' => 'add',
        'id' => 'elem_one_zero',
        'title' => 'Title one.zero',
        'toolTip' => 'toolTip 1',
        'module' => 'Module_One',
        'sortOrder' => 90,
        'action' => 'adminhtml/system',
        'resource' => 'Module_One::one_zero',
        'dependsOnModule' => 'Module_One',
        'dependsOnConfig' => '/one/two',
        ),
    array (
        'type' => 'add',
        'id' => 'elem_one_one',
        'title' => 'Title one.one',
        'toolTip' => 'toolTip 2',
        'module' => 'Module_One',
        'sortOrder' => 90,
        'action' => 'adminhtml/system',
        'resource' => 'Module_One::one_one',
        'parent' => 'elem_one_zero',
        ),
    array (
        'type' => 'update',
        'id' => 'elem_one_zero',
        'title' => 'Title one.zero update',
        'toolTip' => 'toolTip 3',
        'module' => 'Module_One_Update',
        'sortOrder' => 90,
        'action' => 'adminhtml/system',
        'parent' => 'elem_one_zero',
        ),
    array (
        'type' => 'remove',
        'id' => 'elem_one_one',
        ),
    array (
        'type' => 'add',
        'id' => 'elem_two_zero',
        'title' => 'Title two.zero',
        'toolTip' => 'toolTip 4',
        'module' => 'Module_Two',
        'resource' => 'Module_Two::two_zero',
        'sortOrder' => 90,
        'action' => 'adminhtml/system',
        ),
    array (
        'type' => 'add',
        'id' => 'elem_two_two',
        'title' => 'Title two.two',
        'toolTip' => 'toolTip 5',
        'module' => 'Module_Two',
        'sortOrder' => 90,
        'action' => 'adminhtml/system',
        'resource' => 'Module_Two::two_two',
        'parent' => 'elem_two_zero',
        ),
    array (
        'type' => 'update',
        'id' => 'elem_two_zero',
        'title' => 'Title two.zero update',
        'toolTip' => 'toolTip 6',
        'module' => 'Module_Two_Update',
        'sortOrder' => 90,
        'action' => 'adminhtml/system',
        'parent' => 'elem_two_zero',
        ),
    array (
        'type' => 'remove',
        'id' => 'elem_two_two',
        ),
);
