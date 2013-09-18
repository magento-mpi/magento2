<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form element renderer to display link element
 *
 * @method array getValues()
 */
namespace Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form\Element;

class Links
    extends \Magento\Data\Form\Element\AbstractElement
{
    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param array $attributes
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        $attributes = array()
    ) {
        parent::__construct($coreData, $factoryElement, $factoryCollection, $attributes);
        $this->setType('links');
    }

    /**
     * Generates element html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $values = $this->getValues();
        $links = array();
        if ($values) {
            foreach ($values as $option) {
                $links[] = $this->_optionToHtml($option);
            }
        }

        $html = sprintf('<div id="%s" %s>%s%s</div><br />%s%s',
            $this->getHtmlId(),
            $this->serialize($this->getHtmlAttributes()),
            PHP_EOL,
            join('', $links),
            PHP_EOL,
            $this->getAfterElementHtml()
        );
        return $html;
    }

    /**
     * Generate list of links for element content
     *
     * @param array $option
     * @return string
     */
    protected function _optionToHtml(array $option)
    {
        $allowedAttribute = array('href', 'target', 'title', 'style');
        $attributes = array();
        foreach ($option as $title => $value) {
            if (!in_array($title, $allowedAttribute)) {
                continue;
            }
            $attributes[] = $title . '="' . $this->_escape($value) . '"';
        }

        $html = sprintf('<a %s>%s</a>%s',
            join(' ', $attributes),
            $this->_escape($option['label']),
            isset($option['delimiter']) ? $option['delimiter'] : ''
        );

        return $html;
    }

    /**
     * Prepare array of anchor attributes
     *
     * @return array
     */
    public function getHtmlAttributes()
    {
        return array('rel', 'rev', 'accesskey', 'class', 'style', 'tabindex', 'onmouseover',
                     'title', 'xml:lang', 'onblur', 'onclick', 'ondblclick', 'onfocus', 'onmousedown',
                     'onmousemove', 'onmouseout', 'onmouseup', 'onkeydown', 'onkeypress', 'onkeyup');
    }
}
