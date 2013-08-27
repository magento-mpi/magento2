<?php
/**
 * Template engine that enables Twig templates to be used for rendering.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_TemplateEngine_Twig implements Magento_Core_Model_TemplateEngine_EngineInterface,
    Magento_Core_Model_TemplateEngine_BlockTrackerInterface
{
    /**
     * @var Magento_Core_Block_Template
     */
    protected $_currentBlock;

    /**
     * @var Magento_Core_Model_TemplateEngine_Twig_EnvironmentFactory
     */
    protected $_factory;

    /**
     * @var Magento_Core_Model_TemplateEngine_Twig_Extension
     */
    protected $_extension;

    /**
     * @var Twig_Environment
     */
    protected $_environment;

    /**
     *  Populates the environment based on the environment builder provided.
     *
     * @param Magento_Core_Model_TemplateEngine_Twig_EnvironmentFactory $factory
     * @param Magento_Core_Model_TemplateEngine_Twig_Extension $extension
     */
    public function __construct(
        Magento_Core_Model_TemplateEngine_Twig_EnvironmentFactory $factory,
        Magento_Core_Model_TemplateEngine_Twig_Extension $extension
    ) {
        $this->_factory = $factory;
        $this->_extension = $extension;

        $this->_extension->setBlockTracker($this);
    }

    /**
     * Render the named Twig template using the given block as the context of the Twig helper functions/filters.
     *
     * @param Magento_Core_Block_Template $block
     * @param string $fileName
     * @param array $dictionary
     * @throws Exception 
     * @return string
     */
    public function render(Magento_Core_Block_Template $block, $fileName, array $dictionary = array())
    {
        if ($this->_environment === null) {
            $this->_environment = $this->_factory->create();
        }
        $dictionary['block'] = $block;
        // save state from previous block
        $previousBlock = $this->_currentBlock;
        $this->_currentBlock = $block;
        try {
            $output = $this->_environment->render($fileName, $dictionary);
        } catch (Exception $renderException) {
            // restore state for previous block
            $this->_currentBlock = $previousBlock;
            throw $renderException;     
        }
        // restore state for previous block
        $this->_currentBlock = $previousBlock;
        return $output;
    }

    /**
     * Get the current block
     *
     * @return Magento_Core_Block_Template
     */
    public function getCurrentBlock()
    {
        return $this->_currentBlock;
    }
}
