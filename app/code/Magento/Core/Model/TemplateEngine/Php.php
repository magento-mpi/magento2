<?php
/**
 * Template engine that enables PHP templates to be used for rendering
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\TemplateEngine;

class Php implements \Magento\Core\Model\TemplateEngine\EngineInterface
{
    /**
     * @var \Magento\Core\Block\Template
     */
    protected $_currentBlock;

    /**
     * Include the named PHTML template using the given block as the $this
     * reference, though only public methods will be accessible.
     *
     * @param \Magento\Core\Block\Template $block
     * @param string                   $fileName
     * @param array                    $dictionary
     *
     * @return string
     * @throws \Exception any exception that the template may throw
     */
    public function render(\Magento\Core\Block\Template $block, $fileName, array $dictionary = array())
    {
        ob_start();
        try {
            $tmpBlock = $this->_currentBlock;
            $this->_currentBlock = $block;
            extract($dictionary, EXTR_SKIP);
            include $fileName;
            $this->_currentBlock = $tmpBlock;
        } catch (\Exception $exception) {
            ob_end_clean();
            throw $exception;
        }
        /** Get output buffer. */
        $output = ob_get_clean();
        return $output;
    }

    /**
     * Redirects methods calls to the current block.  This is needed because
     * the templates are included in the context of this engine rather than
     * in the context of the block.
     *
     * @param   string $method
     * @param   array  $args
     *
     * @return  mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->_currentBlock, $method), $args);
    }

    /**
     * Redirects isset calls to the current block.  This is needed because
     * the templates are included in the context of this engine rather than
     * in the context of the block.
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_currentBlock->$name);
    }

    /**
     * Allows read access to properties of the current block.  This is needed
     * because the templates are included in the context of this engine rather
     * than in the context of the block.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->_currentBlock->$name;
    }
}
