<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Page orientation source model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Source_PageOrientation
{
    /**
     * Returns all available options with labels
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $orientations = Mage::getModel('Saas_PrintedTemplate_Model_Config')->getConfigSectionArray('page_orientation');

        foreach ($orientations as $key => $item) {
            $options[$key] = Mage::helper('Saas_PrintedTemplate_Helper_Data')->__($item['label']);
        }

        return $options;
    }
}
