<?php
/**
 * Test data structure fixture
 *
 * @copyright {}
 */
class NamespaceA_ModuleA_Webapi_ModuleA_DataStructure
{
    /**
     * String param.
     *
     * @var string inline doc.
     */
    public $stringParam;

    /**
     * Integer param.
     *
     * @var int
     */
    public $integerParam;

    /**
     * Optional string param.
     *
     * @var string
     */
    public $optionalParam = 'default';

    /**
     * Recursive link to self.
     *
     * @var NamespaceA_ModuleA_Webapi_ModuleA_DataStructure
     */
    public $linkToSelf;

    /**
     * Recursive link to array of selves.
     *
     * @var NamespaceA_ModuleA_Webapi_ModuleA_DataStructure[]
     */
    public $linkToArrayOfSelves;

    /**
     * Link to complex type which has link to this type.
     *
     * @var NamespaceA_ModuleA_Webapi_ModuleA_DataStructureB
     */
    public $loopLink;

    /**
     * Link to array of loops
     *
     * @var NamespaceA_ModuleA_Webapi_ModuleA_DataStructureB[]
     * @optional true
     */
    public $loopArray;
}
