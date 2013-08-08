<?php
/**
 * A twig extension for Magento
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_TemplateEngine_Twig_Extension
    extends Twig_Extension
{
    const MAGENTO = 'Magento';

    /**
     * @var Magento_Core_Model_TemplateEngine_Twig_LayoutFunctions
     */
    protected $_layoutFunctions;

    /**
     * @var Magento_Core_Model_TemplateEngine_Twig_CommonFunctions
     */
    protected $_commonFunctions;

    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @var Magento_Core_Model_TemplateEngine_BlockTrackerInterface
     */
    private $_blockTracker;

    /**
     * Create new Extension
     *
     * @param Magento_Core_Model_TemplateEngine_Twig_CommonFunctions $commonFunctions
     * @param Magento_Core_Model_TemplateEngine_Twig_LayoutFunctions $layoutFunctions
     * @param Magento_Core_Model_Translate $translate
     */
    public function __construct(
        Magento_Core_Model_TemplateEngine_Twig_CommonFunctions $commonFunctions,
        Magento_Core_Model_TemplateEngine_Twig_LayoutFunctions $layoutFunctions,
        Magento_Core_Model_Translate $translate
    ) {
        $this->_commonFunctions = $commonFunctions;
        $this->_layoutFunctions = $layoutFunctions;
        $this->_translator = $translate;
    }

    /**
     * Define the name of the extension to be used in Twig environment
     *
     * @return string
     */
    public function getName()
    {
        return self::MAGENTO;
    }

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getFunctions()
    {
        $functions = $this->_commonFunctions->getFunctions();
        $functions = array_merge($functions, $this->_layoutFunctions->getFunctions());

        return $functions;
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        $options = array('is_safe' => array('html'));
        return array(
            new Twig_SimpleFilter('translate', array($this, 'translate'), $options),
        );
    }

    /**
     * Translate block sentence
     *
     * @return string
     */
    public function translate()
    {
        $currentModuleName =  Magento_Core_Block_Abstract::extractModuleName(
            get_class($this->_blockTracker->getCurrentBlock())
        );
        $args = func_get_args();
        $expr = new Magento_Core_Model_Translate_Expr(array_shift($args), $currentModuleName);
        array_unshift($args, $expr);
        return $this->_translator->translate($args);
    }

    /**
     * Sets the block tracker
     *
     * @param Magento_Core_Model_TemplateEngine_BlockTrackerInterface $blockTracker
     */
    public function setBlockTracker(Magento_Core_Model_TemplateEngine_BlockTrackerInterface $blockTracker)
    {
        $this->_blockTracker = $blockTracker;
        // Need to inject this dependency at runtime to avoid cyclical dependency
        $this->_layoutFunctions->setBlockTracker($blockTracker);
    }

}