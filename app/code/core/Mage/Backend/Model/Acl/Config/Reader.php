<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Config readers. Stub class
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Acl_Config_Reader
{
    /**
     *
     * @return DOMDocument
     */
    public function getMergedAclResources()
    {
        $dom = new DOMDocument();
        $config = $dom->createElement('config');
        $dom->appendChild($config);

        $acl = $dom->createElement('acl');
        $config->appendChild($acl);

        $resources = $dom->createElement('resources');
        $acl->appendChild($resources);

        return $dom;
    }
}
