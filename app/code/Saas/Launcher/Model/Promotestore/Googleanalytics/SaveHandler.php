<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Save handler for Googleanalytics Tile
 *
 * @category   Magento
 * @package    Saas_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Model_Promotestore_Googleanalytics_SaveHandler
    extends Saas_Launcher_Model_Tile_ConfigBased_SaveHandlerAbstract
{
    /**
     * Retrieve the list of names of the related configuration sections
     *
     * @return array
     */
    public function getRelatedConfigSections()
    {
        return array('google');
    }

    /**
     * Prepare Data for system configuration
     *
     * @param array $data
     * @return array
     */
    public function prepareData(array $data)
    {
        $groups = $data['groups'];
        if (isset($groups['google']['analytics']['fields']['account']['value'])
            && !empty($groups['google']['analytics']['fields']['account']['value'])
        ) {
            $groups['google']['analytics']['fields']['active']['value'] = 1;
        }
        return $groups;
    }
}
