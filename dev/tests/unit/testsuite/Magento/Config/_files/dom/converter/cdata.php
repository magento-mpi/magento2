<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'root' => array(
        array(
            'simple' => array(
                array(
                    'node_two' => array(
                        array('__content__' => 'valueOne'),
                    ),
                ),
            ),
            'cdata' => array(
                array(
                    'node_one' => array(
                        array('__content__' => '<valueTwo>'),
                    ),
                ),
            ),
            'mixed' => array(
                array(
                    'node_one' => array(
                        array(
                            '__attributes__' => array('attributeOne' => '10'),
                            '__content__' => '<valueThree>'
                        ),
                        array(
                            '__attributes__' => array('attributeTwo' => '20'),
                            '__content__' => 'valueFour'
                        ),
                    ),
                ),
            ),
            'mixed_different_names' => array(
                array(
                    'node_one' => array(
                        array('__content__' => 'valueFive'),
                    ),
                    'node_two' => array(
                        array('__content__' => 'valueSix'),
                    ),
                ),
            ),
        ),
    ),
);