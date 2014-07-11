<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestFramework\Indexer;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        self::_storeDbState();
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass()
    {
        self::_getDbInstance()->cleanup();
        self::_restoreDbState();
        parent::tearDownAfterClass();
    }

    /**
     * Store current database state to db dump
     */
    protected static function _storeDbState()
    {
        self::_getDbInstance()->storeDbDump();
    }

    /**
     * Restore current database state to db dump
     */
    protected static function _restoreDbState()
    {
        self::_getDbInstance()->restoreFromDbDump();
    }

    /**
     * Get Database connection instance
     *
     * @return \Magento\TestFramework\Db\AbstractDb
     */
    protected static function _getDbInstance()
    {
        return \Magento\TestFramework\Helper\Bootstrap::getInstance()->getBootstrap()
            ->getApplication()
            ->getDbInstance();
    }
}
