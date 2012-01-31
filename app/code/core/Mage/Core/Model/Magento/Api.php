<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Magento info API
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Magento_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Retrieve information about current Magento installation
     *
     * @return array
     */
    public function info()
    {
        $result = array();
        $isEnterpriseEnabled = Mage::getConfig()->getModuleConfig('Enterprise_Enterprise')->is('active');
        if (!$isEnterpriseEnabled) {
            $result['magento_edition'] = 'Community';
        } else {
            // define magento edition by its license
            $licenseFileName = (string)Mage::getConfig()->getNode('install/eula_file');
            $isProfessional = strpos($licenseFileName, 'LICENSE_PRO') !== false;
            $result['magento_edition'] = $isProfessional ? 'Professional' : 'Enterprise';
        }
        $result['magento_version'] = Mage::getVersion();

        return $result;
    }
}
