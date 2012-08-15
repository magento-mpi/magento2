<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generic backend controller
 */
class Mage_Adminhtml_Controller_Action extends Mage_Backend_Controller_ActionAbstract
{

    /**
     * Used module name in current adminhtml controller
     */
    protected $_usedModuleName = 'adminhtml';

    /**
     * Currently used area
     *
     * @var string
     */
    protected $_currentArea = 'adminhtml';

    /**
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * Constructor
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs
     */
    public function __construct(Zend_Controller_Request_Abstract $request,
                                Zend_Controller_Response_Abstract $response,
                                array $invokeArgs = array()
    ) {
        parent::__construct($request, $response, $invokeArgs);

        $this->_translator = isset($invokeArgs['translator']) ? $invokeArgs['translator'] : $this->_getTranslator();
    }

    /**
     * Translate a phrase
     *
     * @return string
     */
    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $this->getUsedModuleName());
        array_unshift($args, $expr);
        return $this->_getTranslator()->translate($args);
    }

    /**
     * Get translator model
     *
     * @return Mage_Core_Model_Translate
     */
    protected function _getTranslator()
    {
        if (null === $this->_translator) {
            $this->_translator = Mage::app()->getTranslator();
        }
        return $this->_translator;
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
     * @return Mage_Adminhtml_Controller_Action
     */
    public function setUsedModuleName($moduleName)
    {
        $this->_usedModuleName = $moduleName;
        return $this;
    }
}
