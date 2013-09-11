<?php
/**
 * Abstract helper context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Helper;

class Context implements \Magento\ObjectManager\ContextInterface
{
    /**
     * @var \Magento\Core\Model\Translate
     */
    protected $_translator;

    /**
     * @var \Magento\Core\Model\ModuleManager
     */
    protected $_moduleManager;

    /**
     * @param \Magento\Core\Model\Translate $translator
     * @param \Magento\Core\Model\ModuleManager $moduleManager
     */
    public function __construct(\Magento\Core\Model\Translate $translator, \Magento\Core\Model\ModuleManager $moduleManager)
    {
        $this->_translator = $translator;
        $this->_moduleManager = $moduleManager;
    }

    /**
     * @return \Magento\Core\Model\Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }

    /**
     * @return \Magento\Core\Model\ModuleManager
     */
    public function getModuleManager()
    {
        return $this->_moduleManager;
    }
}
