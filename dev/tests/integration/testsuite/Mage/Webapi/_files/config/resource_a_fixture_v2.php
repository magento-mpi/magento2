<?php
/**
 * Fixture of processed API action controller for resource config.
 * Controller files is at _files/controllers/Webapi/ResourceAController.php
 *
 * @copyright {}
 */
return array(
    'operations' => array(
        'create' => array(
            'documentation' => "Short description.
Long description.
Multiline <b>with html</b>.",
            'interface' => array(
                'in' => array(
                    'parameters' => array(
                        'resourceData' => array(
                            'type' => 'namespaceAModuleADataStructure',
                            'required' => true,
                            'documentation' => 'Data of the resource'
                        )
                    )
                ),
                'out' => array(
                    'result' => array(
                        'type' => 'integer',
                        'documentation' => 'ID of created resource'
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
                            'type' => 'integer',
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
                    'result' => array(
                        'type' => 'namespaceAModuleADataStructure',
                        'documentation' => 'data of resource'
                    )
                ),
            )
        ),
    )
);
