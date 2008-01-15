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
 * @category   Mage
 * @package    Mage_Media
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Media library Image model
 *
 * @category   Mage
 * @package    Mage_Media
 * @author     Ivan Chepurnyi <ivan.chepurnoy@varien.com>
 */
class Mage_Media_Model_Image extends Mage_Core_Model_Abstract
{
    /**
     * Image config instance
     *
     * @var Mage_Media_Model_Image_Config_Interface
     */
    protected $_config;

    /**
     * Image resource
     *
     * @var resource
     */
    protected $_image;

    /**
     * Tmp image resource
     *
     * @var resource
     */
    protected $_tmpImage;

    protected function _construct()
    {
        $this->_init('media/image');
    }

    /**
     * Set media image config instance
     *
     * @param Mage_Media_Model_Image_Config_Interface $config
     * @return unknown
     */
    public function setConfig(Mage_Media_Model_Image_Config_Interface $config)
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Retrive media image config instance
     *
     * @return Mage_Media_Model_Image_Config_Interface
     */
    public function getConfig()
    {
        return $this->_config;
    }

    public function getImage()
    {
        if(is_null($this->_image)) {
            $this->_image = $this->_getResource()->getImage($object);
        }

        return $this->_image;
    }

    public function getTmpImage()
    {
        if(is_null($this->_image)) {
            $this->_tmpImage = $this->_getResource()->getTmpImage($this);
        }

        return $this->_tmpImage;
    }

    /**
     * Retrive source dimensions object
     *
     * @return Varien_Object
     */
    public function getDimensions()
    {
        if(!$this->getData('dimensions')) {
            $this->setData('dimensions', $this->_getResource()->getDimensions($this));
        }
        return $this->getData('dimensions');
    }

    /**
     * Retrive destanation dimensions object
     *
     * @return Varien_Object
     */
    public function getDestanationDimensions()
    {
        if(!$this->getData('destanation_dimensions')) {
            $this->setData('destanation_dimensions', clone $this->getDimensions());
        }

        return $this->getData('destanation_dimensions');
    }

    public function getExtension()
    {
        return substr($this->getFileName(), strrpos($this->getFileName(), '.'));
    }

    public function getFilePath()
    {
        return $this->getConfig()->getBaseMediaPath() . DS . $this->getFileName();
    }

} // Class Mage_Media_Model_Image End