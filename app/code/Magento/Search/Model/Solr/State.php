<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Search_Model_Solr_State
{
    /**
     * Return if solr extension is loaded
     *
     * @return bool
     */
    public function isActive()
    {
        return extension_loaded('solr');
    }
}
