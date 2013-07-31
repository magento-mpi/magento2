<?php
/**
 * A twig extension for Magento
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_TemplateEngine_Twig_Extension extends Twig_Extension
{
    const MAGENTO = 'Magento';

    /**
     * @var Mage_Core_Model_TemplateEngine_Twig_LayoutFunctions
     */
    protected $_layoutFunctions;

    /**
     * @var Mage_Core_Model_TemplateEngine_Twig_CommonFunctions
     */
    protected $_commonFunctions;

    /**
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @var Mage_Core_Model_TemplateEngine_BlockTrackerInterface
     */
    private $_blockTracker;

    /**
     * Create new Extension
     *
     * @param Mage_Core_Model_TemplateEngine_Twig_CommonFunctions $commonFunctions
     * @param Mage_Core_Model_TemplateEngine_Twig_LayoutFunctions $layoutFunctions
     * @param Mage_Core_Model_Translate $translate
     */
    public function __construct(
        Mage_Core_Model_TemplateEngine_Twig_CommonFunctions $commonFunctions,
        Mage_Core_Model_TemplateEngine_Twig_LayoutFunctions $layoutFunctions,
        Mage_Core_Model_Translate $translate
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
        return $this->_translator->translate(func_get_args());
    }

    /**
     * Sets the block tracker
     *
     * @param Mage_Core_Model_TemplateEngine_BlockTrackerInterface $blockTracker
     */
    public function setBlockTracker(Mage_Core_Model_TemplateEngine_BlockTrackerInterface $blockTracker)
    {
        $this->_blockTracker = $blockTracker;
        // Need to inject this dependency at runtime to avoid cyclical dependency
        $this->_layoutFunctions->setBlockTracker($blockTracker);
    }

}
