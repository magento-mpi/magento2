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
 * Initialize Mage_App class.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Initializer_App extends Mage_PHPUnit_Initializer_Abstract
{
    /**
     * Default app() runCode
     *
     * @var string
     */
    const DEFAULT_RUN_CODE = '1';

    /**
     * Default app() runType
     *
     * @var string
     */
    const DEFAULT_RUN_TYPE = 'store';

    /**
     * Mage app() runCode
     *
     * @var string
     */
    protected $_mageRunCode;

    /**
     * Mage app() runType
     *
     * @var string
     */
    protected $_mageRunType;

    /**
     * Mage app() runOptions
     *
     * @var array
     */
    protected $_mageRunOptions;

    /**
     * Flag if we should load data from tables in SQL selects in resource models
     *
     * @var bool
     */
    protected $_loadDataFromTables;

    /**
     * Fixtures XML paths
     *
     * @var array
     */
    protected $_fixtures;

    /**
     * Previous object's hash code.
     * Needed to automatically reset cache before run()
     *
     * @var string
     */
    protected static $_previousLoadedHash;

    /**
     * Runs initialization process.
     */
    public function run()
    {
        $this->reset();
        $this->initDefault();
        $this->_resetCacheAuto();
        $this->_runApp();
    }

    /**
     * Runs Mage::app()
     */
    protected function _runApp()
    {
        $connection = $this->getFixtureConnection()
            ->loadFixtures($this->getFixtures());

        $origLoadFromTable = $connection->getLoadFromTable();
        $connection->setLoadFromTable($this->getLoadDataFromTables());

        Mage::isInstalled(array('etc_dir' => $this->_getEtcDirFromRoot()));

        Mage::app($this->getRunCode(), $this->getRunType(), $this->getRunOptions());

        $connection->setLoadFromTable($origLoadFromTable);
    }

    /**
     * Init object's state by default values if they wasn't set
     */
    public function initDefault()
    {
        if (is_null($this->_mageRunCode)) {
            $this->_mageRunCode = self::DEFAULT_RUN_CODE;
        }

        if (is_null($this->_mageRunType)) {
            $this->_mageRunType = self::DEFAULT_RUN_TYPE;
        }

        if (is_null($this->_mageRunOptions)) {
            $this->_mageRunOptions = array('etc_dir' => $this->getDefaultEtcDir());
        }

        if (is_null($this->_fixtures)) {
            $this->_fixtures = $this->getDefaultFixture();
        }

        if (is_null($this->_loadDataFromTables)) {
            $this->_loadDataFromTables = true;
        }
    }

    /**
     * Calculate and return absolute path for tests etc directory relative default root path
     *
     * @return string
     */
    protected function _getEtcDirFromRoot()
    {
        Mage::setRoot();

        $etcDir = $this->getTestEtcDir();
        if (!$etcDir) {
            $etcDir = $this->getDefaultEtcDir();
        }
        $difference = implode(DS, array_fill(0, substr_count(Mage::getRoot(), DS), '..'));
        $absoluteEtcDir = $difference . substr($etcDir, strpos($etcDir, DS));

        return $absoluteEtcDir;
    }

    /**
     * Calculates object's hash
     *
     * @return string
     */
    protected function _getHash()
    {
        return $this->getRunCode()
            . $this->getRunType()
            . serialize($this->getRunOptions())
            . serialize($this->getFixtures())
            . ((int)$this->getLoadDataFromTables());
    }

    /**
     * Rollback all changes after the test is ended (on tearDown)
     */
    public function reset()
    {
        $this->_resetFixtures();
        $this->_resetApp();
    }

    /**
     * Resets fixture data
     */
    protected function _resetFixtures()
    {
        Mage_PHPUnit_Db_FixtureConnection::getInstance()->reset();
    }

    /**
     * Resets Mage app
     */
    protected function _resetApp()
    {
        Mage::reset();
    }

    /**
     * Automatically resets cache before run if needed.
     * Should be run after initDefault() to calculate right object's hash
     */
    protected function _resetCacheAuto()
    {
        $hash = $this->_getHash();
        if (self::$_previousLoadedHash != $hash) {
            $this->resetCache();
            self::$_previousLoadedHash = $hash;
        }
    }

    /**
     * Force to reset cache.
     */
    public function resetCache()
    {
        Mage_PHPUnit_Cache_Memory::staticClean();
        self::$_previousLoadedHash = null;
    }

    /**
     * Returns default 'etc_dir' needed for app()
     *
     * @return string
     */
    public function getDefaultEtcDir()
    {
        return Mage_PHPUnit_Config::getInstance()->getDefaultEtcDir();
    }

    /**
     * Returns default fixture XML needed to init website and stores
     *
     * @return string
     */
    public function getDefaultFixture()
    {
        return Mage_PHPUnit_Config::getInstance()->getDefaultFixture();
    }

    /**
     * Returns fixture connection (local "database" server object)
     *
     * @return Mage_PHPUnit_Db_FixtureConnection
     */
    public function getFixtureConnection()
    {
        return Mage_PHPUnit_Db_FixtureConnection::getInstance();
    }

    /**
     * Returns run code
     *
     * @return string
     */
    public function getRunCode()
    {
        return $this->_mageRunCode;
    }

    /**
     * Sets runCode needed for app()
     *
     * @param string $runCode
     * @return Mage_PHPUnit_Initializer_App
     */
    public function setRunCode($runCode)
    {
        $this->_mageRunCode = $runCode;
        return $this;
    }

    /**
     * Returns run type
     *
     * @return string
     */
    public function getRunType()
    {
        return $this->_mageRunType;
    }

    /**
     * Sets runType needed for app()
     *
     * @param string $runType
     * @return Mage_PHPUnit_Initializer_App
     */
    public function setRunType($runType)
    {
        $this->_mageRunType = $runType;
        return $this;
    }

    /**
     * Returns run options
     *
     * @return array
     */
    public function getRunOptions()
    {
        return $this->_mageRunOptions;
    }

    /**
     * Sets runOptions needed for app()
     *
     * @param array $runOptions
     * @return Mage_PHPUnit_Initializer_App
     */
    public function setRunOptions($runOptions)
    {
        $this->_mageRunOptions = $runOptions;
        return $this;
    }

    /**
     * Returns etc_dir option for testing environment
     *
     * @return string|null
     */
    public function getTestEtcDir()
    {
        return isset($this->_mageRunOptions['etc_dir']) ? $this->_mageRunOptions['etc_dir'] : null;
    }

    /**
     * Sets etc_dir option needed for app()
     *
     * @param string $etcDir
     * @return Mage_PHPUnit_Initializer_App
     */
    public function setTestEtcDir($etcDir)
    {
        if (!is_array($this->_mageRunOptions)) {
            $this->_mageRunOptions = array();
        }
        $this->_mageRunOptions['etc_dir'] = $etcDir;
        return $this;
    }

    /**
     * Sets fixture(s) XML path(s)
     *
     * @param array|string $fixtures
     * @return Mage_PHPUnit_Initializer_App
     */
    public function setFixtures($fixtures)
    {
        $this->_fixtures = $fixtures;
        return $this;
    }

    /**
     * Returns fixture(s) XML path(s)
     *
     * @return array|string
     */
    public function getFixtures()
    {
        return $this->_fixtures;
    }

    /**
     * Sets flag if we should load data from tables for select queries
     *
     * @param bool $loadDataFromTablesFlag
     * @return Mage_PHPUnit_Initializer_App
     */
    public function setLoadDataFromTables($loadDataFromTablesFlag)
    {
        $this->_loadDataFromTables = $loadDataFromTablesFlag;
        return $this;
    }

    /**
     * Returns flag if we should load data from tables for select queries
     *
     * @return bool
     */
    public function getLoadDataFromTables()
    {
        return $this->_loadDataFromTables;
    }
}
