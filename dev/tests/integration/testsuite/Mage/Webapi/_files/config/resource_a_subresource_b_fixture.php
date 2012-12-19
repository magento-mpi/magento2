<?php
/**
 * Fixture of processed API action controller for resource config.
 * Controller files is at _files/controllers/Webapi/ResourceAController.php
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'methods' => array(
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
                    'parameters' => array(
                        'result' => array(
                            'type' => 'NamespaceAModuleAData',
                            'documentation' => 'Data of resource',
                            'required' => true,
                        )
                    ),
                ),
            ),
        ),
        'list' => array(
            'documentation' => 'List description.',
            'interface' => array(
                'in' => array(
                    'parameters' => array(
                        'parentId' => array(
                            'type' => 'int',
                            'required' => 1,
                            'documentation' => 'Id of parent resource'
                        )
                    ),
                ),
                'out' => array(
                    'parameters' => array(
                        'result' => array(
                            'type' => 'NamespaceAModuleAData[]',
                            'documentation' => 'list of resources',
                            'required' => true,
                        )
                    ),
                ),
            ),
        )
    )
);
