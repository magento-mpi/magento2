<?php
/**
 * Fixture of processed API action controller for resource config.
 * Controller files is at _files/controllers/Webapi/ResourceAController.php
 *
 * @copyright {}
 */
return array(
    'methods' => array(
        'get' => array(
            'documentation' => 'Subresource description.',
            'interface' => array(
                'in' => array(
                    'parameters' => array(
                        'subresourceId' => array(
                            'type' => 'integer',
                            'required' => true,
                            'documentation' => 'ID of subresource.'
                        )
                    )
                ),
                'out' => array(
                    'parameters' => array(
                        'result' => array(
                            'type' => 'NamespaceAModuleADataStructure',
                            'documentation' => 'Data of resource'
                        )
                    ),
                ),
            )
        ),
        'list' => array(
            'documentation' => 'List description.',
            'interface' => array(
                'out' => array(
                    'parameters' => array(
                        'result' => array(
                            'type' => 'NamespaceAModuleADataStructure[]',
                            'documentation' => 'list of resources'
                        )
                    ),
                ),
            )
        )
    )
);
