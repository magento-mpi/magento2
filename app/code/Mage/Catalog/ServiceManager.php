<?php

class Mage_Catalog_ServiceManager extends Mage_Core_Service_Manager
{
    public function getCallerId()
    {
        return 'Mage_Catalog';
    }
}
