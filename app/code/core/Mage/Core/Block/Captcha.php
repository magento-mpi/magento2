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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Captcha block factory
 *
 * @category   Core
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Captcha extends Mage_Core_Block_Template
{
    /* @var $_captchaBlock Mage_Core_Block_Template */
    protected $_captchaBlock;

    /**
     * Factory method - returns block instance for current captcha variant
     *
     * @return Mage_Core_Block_Template
     */
    protected function _getBlockInstance()
    {
        if (!$this->_captchaBlock) {
            $classpath = (string)Mage::helper('core/captcha')->getConfigNode('classpath');
            if (!$classpath) {
                $classpath = 'core/captcha_zend';
            }
            $this->_captchaBlock = Mage::app()->getLayout()->createBlock($classpath);
        }
        return $this->_captchaBlock;
    }

    /**
     * Proxy method calls to block instance
     *
     * @param string $name      Method name
     * @param array  $arguments Method arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->_getBlockInstance(), $name), $arguments);
    }
}
