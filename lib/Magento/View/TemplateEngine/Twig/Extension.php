<?php
/**
 * A twig extension for Magento
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\TemplateEngine\Twig;

use Magento\View\TemplateEngine as Engine;

class Extension extends Twig_Extension
{
    const MAGENTO = 'Magento';

    /**
     * @var LayoutFunctions
     */
    protected $_layoutFunctions;

    /**
     * @var CommonFunctions
     */
    protected $_commonFunctions;

    /**
     * @var Engine\BlockTrackerInterface
     */
    protected $_blockTracker;

    /**
     * Create new Extension
     *
     * @param CommonFunctions $commonFunctions
     * @param LayoutFunctions $layoutFunctions
     */
    public function __construct(
        CommonFunctions $commonFunctions,
        LayoutFunctions $layoutFunctions
    ) {
        $this->_commonFunctions = $commonFunctions;
        $this->_layoutFunctions = $layoutFunctions;
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
        return call_user_func_array('__', func_get_args());
    }

    /**
     * Sets the block tracker
     *
     * @param Engine\BlockTrackerInterface $blockTracker
     */
    public function setBlockTracker(Engine\BlockTrackerInterface $blockTracker)
    {
        $this->_blockTracker = $blockTracker;
        // Need to inject this dependency at runtime to avoid cyclical dependency
        $this->_layoutFunctions->setBlockTracker($blockTracker);
    }
}
