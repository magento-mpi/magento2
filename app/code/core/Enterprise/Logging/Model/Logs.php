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
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Logging_Model_Logs extends Varien_Object
{
    /* internal constants */
    const LOGS_EXTENSION  = 'csv';

    /**
     * Directory to save csv dumps
     */
    private $_basePath = null;
    private $_path = null;

    /**
     * Getter for _basePath
     */
    public function getBasePath() 
    {
        if(!$this->_basePath) {
            $path = array(BP, 'var', 'logging', 'archive'); //, date("Y_m"));
            $this->_basePath = implode(DS, $path);
        }
        return $this->_basePath;
    }

    /**
     * path getter
     */
    public function getPath() 
    {
        if (!$this->_path) {
            return $this->getBasePath();
        }
        return $this->_path;
    }

    /**
     * Path setter
     */
    public function setPath($path) 
    {
        $this->_path = $path;
    }

    /**
     * Load backup file info
     *
     * @param string fileName
     * @param string filePath
     * @return Enterprise_Logging_Model_Logs
     */
    public function load($fileName, $filePath)
    {
        list ($time, $type) = explode("_", substr($fileName, 0, strrpos($fileName, ".")));
        $this->addData(array(
            'id'   => $filePath . DS . $fileName,
            'time' => (int)$time,
            'path' => $filePath)
        );
        return $this;
    }

    /**
     * Checks backup file exists.
     *
     * @return boolean
     */
    public function exists()
    {
        return is_file($this->getPath() . DS . $this->getFileName());
    }

    /**
     * Print output
     *
     */
    public function output()
    {
        if (!$this->exists()) {
            return ;
        }

        $ioAdapter = new Varien_Io_File();
        $ioAdapter->open(array('path' => $this->getPath()));

        $ioAdapter->streamOpen($this->getFileName(), 'r');
        while ($buffer = $ioAdapter->streamRead()) {
            echo $buffer;
        }
        $ioAdapter->streamClose();
    }

    /**
     * Calculate size
     */
    public function getSize()
    {
        if (!is_null($this->getData('size'))) {
            return $this->getData('size');
        }

        if ($this->exists()) {
            $this->setData('size', filesize($this->getPath() . DS . $this->getFileName()));
            return $this->getData('size');
        }
        return 0;
    }

}