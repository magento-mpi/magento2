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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Main TestCase class for Magento unit tests.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_PHPUnit_Integration_TestCase extends Mage_PHPUnit_TestCase
{
    /**
     * Prepares initializers.
     * Is called in setUp method first. Can be overridden in testCases to add more initializers.
     */
    protected function prepareInitializers()
    {
        parent::prepareInitializers();
        Mage_PHPUnit_Initializer_Factory::getInitializer('Mage_PHPUnit_Initializer_App')
            ->setFixtures('');
        Mage_PHPUnit_Initializer_Factory::createInitializer('Mage_PHPUnit_Initializer_Transaction');
    }

    /**
     * Returns DB connection object
     *
     * @return Mage_Core_Model_Resource_Abstract
     */
    protected function getConnection()
    {
        $transaction = Mage_PHPUnit_Initializer_Factory::getInitializer('Mage_PHPUnit_Initializer_Transaction');
        if ($transaction) {
            return $transaction->getConnection();
        }
        return Mage_PHPUnit_Config::getInstance()->getDefaultConnection();
    }
}
