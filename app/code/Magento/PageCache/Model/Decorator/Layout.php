<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Model\Decorator;

/**
 * Layout model
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class Layout extends \Magento\Core\Model\Layout
{

    /**
     * Gets HTML of block element
     *
     * @param string $name
     * @return string
     * @throws \Magento\Exception
     */
    protected function _renderBlock($name)
    {
        $block = $this->getBlock($name);
        return $this->_toHtmlWithCacheContainer($block);
    }

    /**
     * @param $namespace
     * @param $staticType
     * @param $dynamicType
     * @param $data
     */
    public function executeRenderer($namespace, $staticType, $dynamicType, $data = array())
    {
        if ($options = $this->getRendererOptions($namespace, $staticType, $dynamicType)) {
            $dictionary = array();
            /** @var $block \Magento\View\Element\Template */
            $block = $this->createBlock($options['type'], '')
                ->setData($data)
                ->assign($dictionary)
                ->setTemplate($options['template'])
                ->assign($data);

            echo $this->_toHtmlWithCacheContainer($block);
        }
    }

    /**
     * @param \Magento\View\Element\AbstractBlock $block
     * @return string
     */
    private function _toHtmlWithCacheContainer($block)
    {
        if ($block) {
            if ($block->isScopePrivate()) {
                $name = $block->getNameInLayout();
                return '<!-- BLOCK ' . $name . ' -->' . '<!-- /BLOCK ' . $name . ' -->';
            } else {
                return $block->toHtml();
            }
        }
        return '';
    }
}
