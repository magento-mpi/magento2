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
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice apia2 route abstract
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Route_Abstract extends Zend_Controller_Router_Route
{
    /**#@+
     * Names for Zend_Controller_Router_Route::__construct params
     */
    const ROUTE_PARAM      = 'route';
    const DEFAULTS_PARAM   = 'defaults';
    const REGS_PARAM       = 'regs';
    const TRANSLATOR_PARAM = 'translator';
    const LOCALE_PARAM     = 'locale';
    /**#@- */

    /*
     * Default values of parent::__construct() params
     *
     * @var array
     */
    protected $_paramsDefaultValues = array(
        self::ROUTE_PARAM      => null,
        self::DEFAULTS_PARAM   => array(),
        self::REGS_PARAM       => array(),
        self::TRANSLATOR_PARAM => null,
        self::LOCALE_PARAM     => null
    );

    /**
     * Process construct param and call parent::__construct() with params
     *
     * @param array $arguments
     */
    public function __construct(array $arguments)
    {
        parent::__construct(
            $this->_getArgumentValue(self::ROUTE_PARAM, $arguments),
            $this->_getArgumentValue(self::DEFAULTS_PARAM, $arguments),
            $this->_getArgumentValue(self::REGS_PARAM, $arguments),
            $this->_getArgumentValue(self::TRANSLATOR_PARAM, $arguments),
            $this->_getArgumentValue(self::LOCALE_PARAM, $arguments)
        );
    }

    /**
     * Retrieve argument value
     *
     * @param string $name argument name
     * @param array $arguments
     * @return mixed
     */
    protected function _getArgumentValue($name, array $arguments)
    {
        return isset($arguments[$name]) ? $arguments[$name] : $this->_paramsDefaultValues[$name];
    }
}
