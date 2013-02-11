<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_PageCache_Model_DesignPackage_Rules
{
    /**
     * Get package name based on design exception rules
     *
     * @param string $exceptions - design exception rules
     * @return null|string
     */
    public function getPackageByUserAgent($exceptions)
    {
        $output = null;
        $rules = $exceptions ? @unserialize($exceptions) : array();
        if (false === empty($rules)) {
            $output = Mage_Core_Model_Design_Package::getPackageByUserAgent($rules);
        }
        return $output;
    }
}
