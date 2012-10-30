<?php
/**
 * Data structure fixture.
 *
 * @copyright {}
 */
class Vendor_ModuleB_Webapi_ModuleB_DataStructure
{
    /**
     * String doc.
     * {callInfo:vendorModuleBCreate:requiredInput:conditionally}
     * {maxLength:255 chars.}
     *
     * @var string
     */
    public $string;

    /**
     * Integer doc.
     * {min:10}{max:100}
     *
     * @var int {callInfo:allCallsExcept(vendorModuleBUpdate):requiredInput:yes}
     */
    public $integer = 5;

    /**
     * Optional bool doc.
     * {summary:this is summary}
     * {seeLink:http://google.com/:link title:link for}
     *
     * @var bool
     */
    public $optionalBool = false;

    /**
     * {tagStatus:some status}
     *
     * @optional
     * @var Vendor_ModuleB_Webapi_ModuleB_Subresource_DataStructure
     */
    public $optionalComplexType;
}
