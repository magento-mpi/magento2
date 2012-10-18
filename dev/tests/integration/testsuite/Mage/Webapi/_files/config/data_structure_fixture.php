<?php
/**
 * Fixture of processed complex type class.
 * Complex type class is at /_files/controllers/Webapi/ModuleA/DataStructure.php
 *
 * @copyright {}
 */
return array(
    'stringParam' => array(
        'type' => 'string',
        'required' => true,
        'default' => null,
        'documentation' => 'String param.',
    ),
    'integerParam' => array(
        'type' => 'integer',
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
        'type' => 'namespaceAModuleADataStructure',
        'required' => true,
        'default' => null,
        'documentation' => 'Recursive link to self.',
    ),
    'linkToArrayOfSelves' => array(
        'type' => 'namespaceAModuleADataStructure[]',
        'required' => true,
        'default' => null,
        'documentation' => 'Recursive link to array of selves.',
    ),
    'loopLink' => array(
        'type' => 'namespaceAModuleADataStructureB',
        'required' => true,
        'default' => null,
        'documentation' => 'Link to complex type which has link to this type.',
    ),
    'loopArray' => array(
        'type' => 'namespaceAModuleADataStructureB[]',
        'required' => true,
        'default' => null,
        'documentation' => 'Link to array of loops',
    ),
);
