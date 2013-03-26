<?php
/**
 * Data structure description for product entity
 * {myAppInfo:hello}
 * {seeLink:http://wiki.magento.com/:link title:link for description}
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Catalog_Service_Parameter_ProductData
{
    /**
     * {summary:This is a summary for sku.<br/>
     * <b>multiline</b>}
     * Product sku.
     * Use this field to set product <b>sku</b>!
     * {maxLength:100}
     * {tagStatus:Reserved}
     * {seeLink:www.google.com:link title:link for description}
     *
     * @var string
     */
    public $sku;

    /**
     * Product name
     * {maxLength:255}
     * {callInfo:productUpdate:requiredInput:conditionally}
     * {callInfo:productCreate:requiredInput:yes}
     *
     * @var string
     * @optional
     */
    public $name;

    /**
     * Product description.
     * {docInstructions:input:noDoc}
     * {docInstructions:output:noDoc}
     *
     * @var string
     * @optional
     */
    public $description;
}
