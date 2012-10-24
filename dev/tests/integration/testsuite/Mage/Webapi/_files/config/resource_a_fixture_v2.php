<?php
/**
 * Fixture of processed API action controller for resource config.
 * Controller files is at _files/controllers/Webapi/ResourceAController.php
 *
 * @copyright {}
 */
return array(
    'methods' => array(
        'create' => array(
            'documentation' => "Short description.
Long description.
Multiline <b>with html</b>.",
            'interface' => array(
                'in' => array(
                    'parameters' => array(
                        'resourceData' => array(
                            'type' => 'NamespaceAModuleADataStructure',
                            'required' => true,
                            'documentation' => 'Data of the resource'
                        )
                    )
                ),
                'out' => array(
                    'parameters' => array(
                        'result' => array(
                            'type' => 'int',
                            'documentation' => 'ID of created resource'
                        ),
                    )
                ),
            )
        ),
        'get' => array(
            'documentation' => 'Get resource v2.',
            'interface' => array(
                'in' => array(
                    'parameters' => array(
                        'resourceId' => array(
                            'type' => 'int',
                            'required' => true,
                            'documentation' => 'ID of resource'
                        ),
                        'newParameter' => array(
                            'type' => 'string',
                            'required' => true,
                            'documentation' => 'new parameter in version 2'
                        ),
                    )
                ),
                'out' => array(
                    'parameters' => array(
                        'result' => array(
                            'type' => 'NamespaceAModuleADataStructure',
                            'documentation' => 'data of resource'
                        )
                    ),
                ),
            )
        ),
    )
);
