<?php
/**
 * Application area list
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\AreaList;

class Proxy extends \Magento\Framework\App\AreaList
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Proxied instance name
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Proxied instance
     *
     * @var \Magento\Framework\Locale\Resolver
     */
    protected $_subject = null;

    /**
     * Instance shareability flag
     *
     * @var bool
     */
    protected $_isShared = null;

    /**
     * Proxy constructor
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param string $instanceName
     * @param bool $shared
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        $instanceName = 'Magento\Framework\App\AreaList',
        $shared = true
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
        $this->_isShared = $shared;
    }
    
    /**
     * @return array
     */
    public function __sleep()
    {
        return array('_subject', '_isShared');
    }

    /**
     * Retrieve ObjectManager from global scope
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Clone proxied instance
     *
     * @return void
     */
    public function __clone()
    {
        $this->_subject = clone $this->_getSubject();
    }

    /**
     * Get proxied instance
     *
     * @return \Magento\Framework\Locale\Resolver
     */
    protected function _getSubject()
    {
        if (!$this->_subject) {
            $this->_subject = true === $this->_isShared
                ? $this->_objectManager->get($this->_instanceName)
                : $this->_objectManager->create($this->_instanceName);
        }
        return $this->_subject;
    }


    /**
     * Retrieve area code by front name
     *
     * @param string $frontName
     * @return null|string
     */
    public function getCodeByFrontName($frontName)
    {
        return $this->_getSubject()->getCodeByFrontName($frontName);
    }

    /**
     * Retrieve area front name by code
     *
     * @param string $areaCode
     * @return string
     */
    public function getFrontName($areaCode)
    {
        return $this->_getSubject()->getFrontName($areaCode);
    }

    /**
     * Retrieve area codes
     *
     * @return string[]
     */
    public function getCodes()
    {
        return $this->_getSubject()->getCodes();
    }

    /**
     * Retrieve default area router id
     *
     * @param string $areaCode
     * @return string
     */
    public function getDefaultRouter($areaCode)
    {
        return $this->_getSubject()->getDefaultRouter($areaCode);
    }

    /**
     * Retrieve application area
     *
     * @param   string $code
     * @return  \Magento\Framework\App\Area
     */
    public function getArea($code)
    {
        return $this->_getSubject()->getArea($code);
    }
}
