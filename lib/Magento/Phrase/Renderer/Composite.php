<?php
/**
 * Composite Phrase renderer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Phrase_Renderer_Composite implements Magento_Phrase_RendererInterface
{
    /**
     * Renderer factory
     *
     * @var Magento_Phrase_Renderer_Factory
     */
    protected $_rendererFactory;

    /**
     * App State instance
     *
     * @var Mage_Core_Model_App_State
     */
    protected $_state;

    /**
     * Logger instance
     *
     * @var Mage_Core_Model_Logger
     */
    protected $_logger;

    /**
     * List of Magento_Phrase_RendererInterface
     *
     * @var array
     */
    protected $_renderers = array();

    /**
     * Renderer construct
     *
     * @param Magento_Phrase_Renderer_Factory $rendererFactory
     * @param Mage_Core_Model_App_State $state
     * @param Mage_Core_Model_Logger $logger
     * @param array $renderers
     */
    public function __construct(
        Magento_Phrase_Renderer_Factory $rendererFactory,
        Mage_Core_Model_App_State $state,
        Mage_Core_Model_Logger $logger,
        array $renderers = array()
    ) {
        $this->_rendererFactory = $rendererFactory;
        $this->_state = $state;
        $this->_logger = $logger;

        foreach ($renderers as $render) {
            $this->append($render);
        }
    }

    /**
     * Add renderer to the end of the chain
     *
     * @param Magento_Phrase_RendererInterface|string $render
     * @throws InvalidArgumentException
     */
    public function append($render)
    {
        if (is_string($render)) {
            $render = $this->_rendererFactory->create($render);
        } elseif (!$render instanceof Magento_Phrase_RendererInterface) {
            throw new InvalidArgumentException('Wrong renderer ' . get_class($render));
        }

        array_push($this->_renderers, $render);
    }

    /**
     * {@inheritdoc}
     */
    public function render($text, array $arguments = array())
    {
        /** @var Magento_Phrase_Renderer_Composite $render */
        foreach ($this->_renderers as $render) {
            $text = $this->_singleRenderingProcessing($render, $text, $arguments);
        }
        return $text;
    }

    /**
     * Single rendering by collection element
     *
     * @param Magento_Phrase_RendererInterface $render
     * @param string $text
     * @param array $arguments
     * @return string
     */
    protected function _singleRenderingProcessing($render, $text, $arguments)
    {
        try {
            $text = $render->render($text, $arguments);
        } catch (Exception $renderException) {
            $this->_processingException($renderException);
        }
        return $text;
    }

    /**
     * Processing exception
     *
     * @param Exception $renderException
     */
    protected function _processingException($renderException)
    {
        if ($this->_isDeveloperMode()) {
            $this->_logException($renderException);
        }
    }

    /**
     * Check is developer mode
     *
     * @return bool
     */
    protected function _isDeveloperMode()
    {
        return Mage_Core_Model_App_State::MODE_DEVELOPER == $this->_state->getMode();
    }

    /**
     * Log exception
     *
     * @param Exception $renderException
     */
    protected function _logException($renderException)
    {
        $this->_logger->logException($renderException);
    }
}
