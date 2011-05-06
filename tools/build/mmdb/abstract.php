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
 * @category    Magento
 * @package     Magento_Tools
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 *
 */
abstract class Mage_Tools_Build_Abstract
{
    /**
     * Input arguments
     *
     * @var array
     */
    protected $_args        = array();

    /**
     * Initialize shell and parse input parameters
     *
     */
    public function __construct()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            die('This script cannot be run from Browser. This is the shell script.');
        }

        $this->_parseArgs();
        $this->_construct();
    }

    /**
     * Parse input arguments
     *
     * @return Mage_Shell_Abstract
     */
    protected function _parseArgs()
    {
        $current = null;
        foreach ($_SERVER['argv'] as $arg) {
            $match = array();
            if (preg_match('#^--([\w\d_-]{1,})$#', $arg, $match) || preg_match('#^-([\w\d_]{1,})$#', $arg, $match)) {
                $current = $match[1];
                $this->_args[$current] = true;
            } else {
                if ($current) {
                    $this->_args[$current] = $arg;
                } else if (preg_match('#^([\w\d_]{1,})$#', $arg, $match)) {
                    $this->_args[$match[1]] = true;
                }
            }
        }
        return $this;
    }

    /**
     * Additional initialize instruction
     *
     * @return Mage_Shell_Abstract
     */
    protected function _construct()
    {
        return $this;
    }

    /**
     * Retrieve argument value by name or false
     *
     * @param string $name the argument name
     * @param mixed $default the default value
     * @return mixed
     */
    public function getArg($name, $default = false)
    {
        if (isset($this->_args[$name])) {
            return $this->_args[$name];
        }
        return $default;
    }

    /**
     * Run build application
     *
     * @return void
     */
    abstract public function prepare();

    /**
     * Change base URLS
     *
     * @return void
     */
    abstract public function baseUrl();

    /**
     * Copy database from build
     *
     * @return void
     */
    abstract public function copyOtherBuildDb();

}
