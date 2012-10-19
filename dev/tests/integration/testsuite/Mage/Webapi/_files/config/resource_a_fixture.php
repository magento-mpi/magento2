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
                    'result' => array(
                        'type' => 'integer',
                        'documentation' => 'ID of created resource'
                    )
                ),
            )
        ),
        'get' => array(
            'documentation' => 'Get resource.',
            'interface' => array(
                'in' => array(
                    'parameters' => array(
                        'resourceId' => array(
                            'type' => 'integer',
                            'required' => true,
                            'documentation' => 'ID of resource'
                        )
                    )
                ),
                'out' => array(
                    'result' => array(
                        'type' => 'NamespaceAModuleADataStructure',
                        'documentation' => 'data of resource'
                    )
                ),
            )
        ),
    ),
);
