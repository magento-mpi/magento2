<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Stub class for Mage_Core_Model_Resource_Config.
 * Needed to load real modules configs.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Stub_Mage_Core_Model_Resource_Config extends Mage_Core_Model_Mysql4_Config
{
    /**
     * Loads real modules config from real project's etc folder
     */
    protected function _initModules()
    {
        $config = Mage::getConfig();
        $etcDir = $config->getOptions()->getEtcDir();
        $rootEtc = BP . DS . 'app' . DS . 'etc';
        $config->getOptions()->setEtcDir($rootEtc);
        $config->loadModules();
        if ($etcDir != $rootEtc) {
            $config->getOptions()->setEtcDir($etcDir);
            $config->loadModules();
        }
    }

    /**
     * Load configuration values into xml config object
     *
     * @param Mage_Core_Model_Config $xmlConfig
     * @param string $condition
     * @return Mage_PHPUnit_Stub_Mage_Core_Model_Resource_Config
     */
    public function loadToXml(Mage_Core_Model_Config $xmlConfig, $condition = null)
    {
        $this->_initModules();

        return parent::loadToXml($xmlConfig, $condition);
    }
}
