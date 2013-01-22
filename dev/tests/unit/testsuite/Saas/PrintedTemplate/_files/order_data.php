<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage unit_tests
 * @copyright  {copyright}
 * @license    {license_link}
 */

return array(
    '1/1' => array(
        1, array(
            array(
                'orderItem' => array(
                    'id' => 1,
                    'parentItem' => array(
                        'id' => 1
                    ),
                ),
                'orderItemId' => 1
            )
        ), array(1)
    ),

    '1/1 - by order item\'s parent id' => array(
        1, array(
            array(
                'orderItem' => array(
                    'id' => 2,
                    'parentItem' => array(
                        'id' => 1
                    ),
                ),
                'orderItemId' => 1
            )
        ), array(1)
    ),

    '0/1' => array(
        2, array(
            array(
                'orderItem' => array(
                    'id' => 1,
                    'parentItem' => array(
                        'id' => 1
                    ),
                ),
                'orderItemId' => 1
            )
        ), array()
    ),

    '2/2' => array(
        1, array(
            array(
                'orderItem' => array(
                    'id' => 1
                ),
                'orderItemId' => 1
            ),

            array(
                'orderItem' => array(
                    'id' => 1,
                    'parentItem' => array(
                        'id' => 1
                    ),
                ),
                'orderItemId' => 2
            )
        ), array(1, 2)
    )
);
