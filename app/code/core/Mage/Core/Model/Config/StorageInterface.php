<?php

interface Mage_Core_Model_Config_StorageInterface
{
    /**
     * Get loaded configuration
     *
     * @return string
     */
    public function getConfiguration($useCache = true);
}
