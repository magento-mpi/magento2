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
            'item' => array(
                array(
                    '__attributes__' => array(
                        'id' => 'id1',
                        'attrZero' => 'value 0',
                    ),
                    '__content__' => 'Item 1.1',
                ),
                array(
                    '__attributes__' => array(
                        'id' => 'id2',
                        'attrOne' => 'value 2',
                    ),
                    'subitem' => array(
                        array(
                            '__attributes__' => array(
                                'id' => 'id2.1',
                                'attrTwo' => 'value 2.1',
                            ),
                            '__content__' => 'Item 2.1',
                        ),
                        array(
                            '__attributes__' => array(
                                'id' => 'id2.2',
                            ),
                            'value' => array(
                                array('__content__' => 1),
                                array('__content__' => 2),
                                array('__content__' => 'test'),
                            ),
                        ),
                    ),
                ),
                array(
                    '__attributes__' => array(
                        'id' => 'id3',
                        'attrThree' => 'value 3',
                    ),
                    '__content__' => 'Item 3.1',
                ),
            ),
        ),
    ),
);