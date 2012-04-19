<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
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
