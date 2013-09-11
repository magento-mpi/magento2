<?php
/**
 * Template engine that enables Twig templates to be used for rendering.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\TemplateEngine;

class Twig implements \Magento\Core\Model\TemplateEngine\EngineInterface,
    \Magento\Core\Model\TemplateEngine\BlockTrackerInterface
{
    /**
     * @var \Magento\Core\Block\Template
     */
    protected $_currentBlock;

    /**
     * @var \Magento\Core\Model\TemplateEngine\Twig\EnvironmentFactory
     */
    protected $_factory;

    /**
     * @var \Magento\Core\Model\TemplateEngine\Twig\Extension
     */
    protected $_extension;

    /**
     * @var \Twig_Environment
     */
    protected $_environment;

    /**
     *  Populates the environment based on the environment builder provided.
     *
     * @param \Magento\Core\Model\TemplateEngine\Twig\EnvironmentFactory $factory
     * @param \Magento\Core\Model\TemplateEngine\Twig\Extension $extension
     */
    public function __construct(
        \Magento\Core\Model\TemplateEngine\Twig\EnvironmentFactory $factory,
        \Magento\Core\Model\TemplateEngine\Twig\Extension $extension
    ) {
        $this->_factory = $factory;
        $this->_extension = $extension;

        $this->_extension->setBlockTracker($this);
    }

    /**
     * Render the named Twig template using the given block as the context of the Twig helper functions/filters.
     *
     * @param \Magento\Core\Block\Template $block
     * @param string $fileName
     * @param array $dictionary
     * @throws \Exception 
     * @return string
     */
    public function render(\Magento\Core\Block\Template $block, $fileName, array $dictionary = array())
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
        } catch (\Exception $renderException) {
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
     * @return \Magento\Core\Block\Template
     */
    public function getCurrentBlock()
    {
        return $this->_currentBlock;
    }
}
