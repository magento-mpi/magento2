<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design mode design editor url model
 */
class Mage_DesignEditor_Model_Url_DesignMode extends Mage_Core_Model_Url
{
    /**
     * Build url by requested path and parameters
     *
     * @param string|null $routePath
     * @param array|null $routeParams
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        return '#';
    }
}
