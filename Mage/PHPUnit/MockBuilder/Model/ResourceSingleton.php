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
 * Class which creates mock object for models when they are created
 * in a code using Mage::getResourceSingleton('...');
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_MockBuilder_Model_ResourceSingleton extends Mage_PHPUnit_MockBuilder_Model_Singleton
{
    /**
     * Singleton key prefix needed for Mage::registry
     *
     * @var string
     */
    protected $_regisrtyKeyPrefix = '_resource_singleton';

    /**
     * Returns PHPUnit model helper.
     *
     * @return Mage_PHPUnit_Helper_Model_ResourceModel
     */
    protected function _getModelHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('model_resourceModel');
    }
}
