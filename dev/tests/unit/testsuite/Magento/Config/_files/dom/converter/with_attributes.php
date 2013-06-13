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
            'node_one' => array(
                array(
                    '__attributes__' => array(
                        'attributeOne' => '10',
                        'attributeTwo' => '20',
                    ),
                    'subnode' => array(
                        array(
                            '__attributes__' => array('attributeThree' => '30'),
                            '__content__' => 'Value1',
                        ),
                        array(
                            '__attributes__' => array('attributeFour' => '40'),
                        ),
                    ),
                    'books' => array(
                        array(
                            '__attributes__' => array('attributeFive' => '50')
                        ),
                    ),
                ),
            ),
        ),
    ),
);
