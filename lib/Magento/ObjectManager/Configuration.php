<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ObjectManager
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_ObjectManager_Configuration
{
    /**
     * Configure di instance
     *
     * @param Magento_ObjectManager $objectManager
     * @param array $runTimeParams
     */
    public function configure(Magento_ObjectManager $objectManager, array $runTimeParams = array());
}
