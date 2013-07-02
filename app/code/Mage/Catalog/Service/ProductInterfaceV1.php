<?php
/**
 * TODO: Fake service for WSDL generation testing purposes.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Catalog_Service_ProductInterfaceV1
{
    public function item($request);

    public function soapOnlyMethod();
}
