<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generic backend controller
 */
class Magento_Adminhtml_Controller_Action extends Magento_Backend_Controller_ActionAbstract
{
    /**
     * Used module name in current adminhtml controller
     */
    protected $_usedModuleName = 'adminhtml';

    /**
     * Translate a phrase
     *
     * @return string
     */
    public function __()
    {
        $args = func_get_args();
        $expr = new Magento_Core_Model_Translate_Expr(array_shift($args), $this->getUsedModuleName());
        array_unshift($args, $expr);
        return $this->_translator->translate($args);
    }

    /**
     * Retrieve currently used module name
     *
     * @return string
     */
    public function getUsedModuleName()
    {
        return $this->_usedModuleName;
    }

    /**
     * Set currently used module name
     *
     * @param string $moduleName
     * @return Magento_Adminhtml_Controller_Action
     */
    public function setUsedModuleName($moduleName)
    {
        $this->_usedModuleName = $moduleName;
        return $this;
    }
}
