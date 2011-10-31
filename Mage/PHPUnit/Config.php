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
 * Test config object.
 * Contains some useful data for test framework.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Config
{
    /**
     * Framework base path
     *
     * @var string
     */
    protected $_libBasePath;

    /**
     * Default etc dir
     *
     * @var string
     */
    protected $_defaultEtcDir;

    /**
     * Default fixture filepath
     *
     * @var string
     */
    protected $_defaultFixture;

    /**
     * Default database connection
     *
     * @var Mage_Core_Model_Resource_Abstract
     */
    protected $_defaultConnection;

    /**
     * Instance of the object
     *
     * @var Mage_PHPUnit_Config
     */
    static protected $_instance;

    /**
     * Creates and returns instance of config
     *
     * @return Mage_PHPUnit_Config
     */
    static public function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Gets unit testing framework base path
     *
     * @return string
     */
    public function getLibBasePath()
    {
        if (!$this->_libBasePath) {
            $this->_libBasePath = realpath(dirname(__FILE__) . DS . '..' . DS . '..');
        }
        return $this->_libBasePath;
    }

    /**
     * Returns default 'etc' dir
     *
     * @return string
     */
    public function getDefaultEtcDir()
    {
        if (is_null($this->_defaultEtcDir)) {
            $this->_defaultEtcDir = $this->getLibBasePath() . DS . '_etc';
        }
        return $this->_defaultEtcDir;
    }

    /**
     * Sets default 'etc' dir
     *
     * @param string $dir
     * @return Mage_PHPUnit_Config
     */
    public function setDefaultEtcDir($dir)
    {
        $this->_defaultEtcDir = $dir;
        return $this;
    }

    /**
     * Returns default xml fixture filepath
     *
     * @return string
     */
    public function getDefaultFixture()
    {
        if (is_null($this->_defaultFixture)) {
            $this->_defaultFixture = $this->getLibBasePath() . DS . '_fixtures' . DS . 'default.xml';
        }
        return $this->_defaultFixture;
    }

    /**
     * Sets default xml fixture filepath
     *
     * @param string $fixturePath
     * @return Mage_PHPUnit_Config
     */
    public function setDefaultFixture($fixturePath)
    {
        $this->_defaultFixture = $fixturePath;
        return $this;
    }

    /**
     * Returns default DB connection
     *
     * @return Mage_Core_Model_Resource_Abstract
     */
    public function getDefaultConnection()
    {
        if (is_null($this->_defaultConnection)) {
            $this->_defaultConnection = Mage::getModel('core/resource')->getConnection(Mage_Core_Model_Resource::DEFAULT_WRITE_RESOURCE);
        }
        return $this->_defaultConnection;
    }

    /**
     * Sets default DB connection
     *
     * @param Mage_Core_Model_Resource_Abstract $connection
     * @return Mage_PHPUnit_Config
     */
    public function setDefaultConnection($connection)
    {
        $this->_defaultConnection = $connection;
        return $this;
    }
}
