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
        'create' => array(
            'documentation' => "Short description.
Long description.
Multiline <b>with html</b>.",
            'interface' => array(
                'in' => array(
                    'parameters' => array(
                        'resourceData' => array(
                            'type' => 'NamespaceAModuleAData',
                            'required' => true,
                            'documentation' => 'Data of the resource'
                        )
                    )
                ),
                'out' => array(
                    'parameters' => array(
                        'result' => array(
                            'type' => 'int',
                            'documentation' => 'ID of created resource',
                            'required' => true,
                        )
                    ),
                ),
            ),
        ),
        'get' => array(
            'documentation' => 'Get resource.',
            'interface' => array(
                'in' => array(
                    'parameters' => array(
                        'resourceId' => array(
                            'type' => 'int',
                            'required' => true,
                            'documentation' => 'ID of resource'
                        )
                    )
                ),
                'out' => array(
                    'parameters' => array(
                        'result' => array(
                            'type' => 'NamespaceAModuleAData',
                            'documentation' => 'data of resource',
                            'required' => true,
                        )
                    ),
                ),
            ),
        ),
    ),
);
