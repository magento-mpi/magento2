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

return array(
    'some.key' => 'some.val',
    'group.1' => array(
        'fields' => array(
            'f1.1' => array('value' => 'f1.1.val'),
            'f1.2' => array('value' => 'f1.2.val'),
            'g1.1' => array('value' => 'g1.1.val')
        )
    ),
    'group.2' => array(
        'fields' => array('f2.1' => array('value' => 'f2.1.val'), 'f2.2' => array('value' => 'f2.2.val')),
        'groups' => array(
            'group.2.1' => array(
                'fields' => array(
                    'f2.1.1' => array('value' => 'f2.1.1.val'), 'f2.1.2' => array('value' => 'f2.1.2.val'),
                ),
                'groups' => array(
                    'group.2.1.1' => array(
                        'fields' => array(
                            'f2.1.1.1' => array('value' => 'f2.1.1.1.val'),
                            'f2.1.1.2' => array('value' => 'f2.1.1.2.val'),
                        )
                    ),
                ),
            ),
        )
    ),
);
