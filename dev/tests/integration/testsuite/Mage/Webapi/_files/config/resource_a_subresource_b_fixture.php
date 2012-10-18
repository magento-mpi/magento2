<?php
/**
 * Fixture of processed API action controller for resource config.
 * Controller files is at _files/controllers/Webapi/ResourceAController.php
 *
 * @copyright {}
 */
return array(
    'operations' => array(
        'get' => array(
            'documentation' => 'Subresource description.',
            'interface' => array(
                'in' => array(
                    'parameters' => array(
                        'subresourceId' => array(
                            'type' => 'int',
                            'required' => true,
                            'documentation' => 'ID of subresource.'
                        )
                    )
                ),
                'out' => array(
                    'result' => array(
                        'type' => 'NamespaceA_ModuleA_Webapi_ResourceA_DataStructure',
                        'documentation' => 'Data of resource'
                    )
                ),
            )
        ),
        'list' => array(
            'documentation' => 'List description.',
            'interface' => array(
                'out' => array(
                    'result' => array(
                        'type' => 'NamespaceA_ModuleA_Webapi_ResourceA_DataStructure[]',
                        'documentation' => 'list of resources'
                    )
                ),
            )
        )
    )
);
