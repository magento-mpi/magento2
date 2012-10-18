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
                            'type' => 'NamespaceA_ModuleA_Webapi_ResourceA_DataStructure',
                            'required' => true,
                            'documentation' => 'Data of the resource'
                        )
                    )
                ),
                'out' => array(
                    'result' => array(
                        'type' => 'int',
                        'documentation' => 'ID of created resource'
                    )
                ),
            )
        ),
    )
);
