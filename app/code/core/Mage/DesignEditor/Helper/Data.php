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
 * Design Editor main helper
 */
class Mage_DesignEditor_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * VDE front name prefix
     */
    const FRONT_NAME = 'vde';

    /**
     * Check if URL has vde prefix
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return bool
     */
    public function isVdeRequest(Mage_Core_Controller_Request_Http $request)
    {
        $url = trim($request->getOriginalPathInfo(), '/');
        return $url == self::FRONT_NAME || strpos($url, self::FRONT_NAME . '/') === 0;
    }
}
