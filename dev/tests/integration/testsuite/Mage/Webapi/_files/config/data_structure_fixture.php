<?php
/**
 * Fixture of processed complex type class.
 * Complex type class is at /_files/controllers/Webapi/ModuleA/DataStructure.php
 *
 * @copyright {}
 */
return array(
    'documentation' => 'Test data structure fixture',
    'parameters' => array(
        'stringParam' => array(
            'type' => 'string',
            'required' => true,
            'default' => null,
            'documentation' => 'inline doc.String param.',
        ),
        'integerParam' => array(
            'type' => 'int',
            'required' => true,
            'default' => null,
            'documentation' => 'Integer param.',
        ),
        'optionalParam' => array(
            'type' => 'string',
            'required' => false,
            'default' => 'default',
            'documentation' => 'Optional string param.',
        ),
        'linkToSelf' => array(
            'type' => 'NamespaceAModuleADataStructure',
            'required' => true,
            'default' => null,
            'documentation' => 'Recursive link to self.',
        ),
        'linkToArrayOfSelves' => array(
            'type' => 'NamespaceAModuleADataStructure[]',
            'required' => true,
            'default' => null,
            'documentation' => 'Recursive link to array of selves.',
        ),
        'loopLink' => array(
            'type' => 'NamespaceAModuleADataStructureB',
            'required' => true,
            'default' => null,
            'documentation' => 'Link to complex type which has link to this type.',
        ),
        'loopArray' => array(
            'type' => 'NamespaceAModuleADataStructureB[]',
            'required' => false,
            'default' => null,
            'documentation' => 'Link to array of loops',
        ),
    ),
);
