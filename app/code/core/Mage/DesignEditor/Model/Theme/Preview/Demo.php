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
 * Visual Design Editor Preview Demo Mode
 */
class Mage_DesignEditor_Model_Theme_Preview_Demo extends  Mage_DesignEditor_Model_Theme_Preview_Abstract
{
    /**
     * Return preview url
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return 'http://www.magentocommerce.com';
    }
}
