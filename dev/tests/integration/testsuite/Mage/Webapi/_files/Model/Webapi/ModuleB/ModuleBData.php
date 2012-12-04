<?php
/**
 * Data structure fixture.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Vendor_ModuleB_Model_Webapi_ModuleBData
{
    /**
     * String doc.
     * {callInfo:vendorModuleBCreate:requiredInput:conditionally}
     * {maxLength:255 chars.}
     *
     * @var string
     */
    public $stringParam;

    /**
     * Integer doc.
     * {min:10}{max:100}
     * {callInfo:vendorModuleBGet:returned:Conditionally}
     *
     * @var int {callInfo:allCallsExcept(vendorModuleBUpdate):requiredInput:yes}
     */
    public $integerParam = 5;

    /**
     * Optional bool doc.
     * {summary:this is summary}
     * {seeLink:http://google.com/:link title:link for}
     * {docInstructions:output:noDoc}
     *
     * @var bool
     */
    public $optionalBool = false;

    /**
     * {tagStatus:some status}
     *
     * @optional
     * @var Vendor_ModuleB_Model_Webapi_ModuleB_SubresourceData[]
     */
    public $complexTypeArray;
}
