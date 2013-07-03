<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
return array(
    array('id' => 'Dummy1::parent', 'title' => 'Dummy Parent Resource', 'module' => 'Dummy1', 'sortOrder' => 0,
        'children' => array(
            array('id' => 'Dummy1::first', 'title' => 'Dummy Resource #1', 'module' => 'Dummy1', 'sortOrder' => '0',
                'children' => array(
                    array(
                        'id' => 'Dummy2::parent', 'title' => 'Dummy 2 Resource Parent', 'module' => 'Dummy2',
                        'sortOrder' => '0',
                        'children' => array(
                            array(
                                'id' => 'Dummy2::first',
                                'title' => 'Dummy 2 Resource #1',
                                'module' => 'Dummy2',
                                'sortOrder' => '10'
                            ),
                            array(
                                'id' => 'Dummy2::second',
                                'title' => 'Dummy 2 Resource #2',
                                'module' => 'Dummy2',
                                'sortOrder' => '20'
                            ),
                        )
                    )
                )
            ),
            array('id' => 'Dummy1::second', 'title' => 'Dummy Resource #2', 'module' => 'Dummy1', 'sortOrder' => '10'),
            array('id' => 'Dummy1::third', 'title' => 'Dummy Resource #3', 'module' => 'Dummy1', 'sortOrder' => '50')
        )
    ),
    array('id' => 'Dummy1::all', 'title' => 'Allow everything', 'module' => 'Dummy1', 'sortOrder' => 0)
);
