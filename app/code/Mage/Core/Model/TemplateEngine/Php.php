<?php
/**
 * Template engine that enables PHP templates to be used for rendering
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_TemplateEngine_Php implements Mage_Core_Model_TemplateEngine_EngineInterface
{
    /**
     * @var Mage_Core_Block_Template
     */
    protected $_currentBlock;

    /**
     * Include the named PHTML template using the given block as the $this
     * reference, though only public methods will be accessible.
     *
     * @param Mage_Core_Block_Template $block
     * @param string                   $fileName
     * @param array                    $dictionary
     *
     * @return string
     * @throws Exception any exception that the template may throw
     */
    public function render(Mage_Core_Block_Template $block, $fileName, array $dictionary = array())
    {
        ob_start();
        try {
            $tmpBlock = $this->_currentBlock;
            $this->_currentBlock = $block;
            extract($dictionary, EXTR_SKIP);
            include $fileName;
            $this->_currentBlock = $tmpBlock;
        } catch (Exception $exception) {
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