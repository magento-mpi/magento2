<?php
/**
 * Test data structure fixture
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class NamespaceA_ModuleA_Model_Webapi_ModuleAData
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
     * @var NamespaceA_ModuleA_Model_Webapi_ModuleAData
     */
    public $linkToSelf;

    /**
     * Recursive link to array of selves.
     *
     * @var NamespaceA_ModuleA_Model_Webapi_ModuleAData[]
     */
    public $linkToArrayOfSelves;

    /**
     * Link to complex type which has link to this type.
     *
     * @var NamespaceA_ModuleA_Model_Webapi_ModuleADataB
     */
    public $loopLink;

    /**
     * Link to array of loops
     *
     * @var NamespaceA_ModuleA_Model_Webapi_ModuleADataB[]
     * @optional true
     */
    public $loopArray;
}
