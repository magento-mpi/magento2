<?php
/**
 * A twig extension for Magento
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\TemplateEngine\Twig;

class Extension extends \Twig_Extension
{
    const MAGENTO = 'Magento';

    /**
     * @var \Magento\Core\Model\TemplateEngine\Twig\LayoutFunctions
     */
    protected $_layoutFunctions;

    /**
     * @var \Magento\Core\Model\TemplateEngine\Twig\CommonFunctions
     */
    protected $_commonFunctions;

    /**
     * @var \Magento\Core\Model\TemplateEngine\BlockTrackerInterface
     */
    private $_blockTracker;

    /**
     * Create new Extension
     *
     * @param \Magento\Core\Model\TemplateEngine\Twig\CommonFunctions $commonFunctions
     * @param \Magento\Core\Model\TemplateEngine\Twig\LayoutFunctions $layoutFunctions
     */
    public function __construct(
        \Magento\Core\Model\TemplateEngine\Twig\CommonFunctions $commonFunctions,
        \Magento\Core\Model\TemplateEngine\Twig\LayoutFunctions $layoutFunctions
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
            new \Twig_SimpleFilter('translate', array($this, 'translate'), $options),
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
     * @param \Magento\Core\Model\TemplateEngine\BlockTrackerInterface $blockTracker
     */
    public function setBlockTracker(\Magento\Core\Model\TemplateEngine\BlockTrackerInterface $blockTracker)
    {
        $this->_blockTracker = $blockTracker;
        // Need to inject this dependency at runtime to avoid cyclical dependency
        $this->_layoutFunctions->setBlockTracker($blockTracker);
    }
}
