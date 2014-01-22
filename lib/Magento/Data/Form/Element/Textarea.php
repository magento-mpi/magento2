<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Data
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Form textarea element
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Form\Element;

use Magento\Escaper;

class Textarea extends AbstractElement
{
    /**
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        $data = array()
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->setType('textarea');
        $this->setExtType('textarea');
        $this->setRows(2);
        $this->setCols(15);
    }

    /**
     * Return the HTML attributes
     *
     * @return string[]
     */
    public function getHtmlAttributes()
    {
        return array('title', 'class', 'style', 'onclick', 'onchange', 'rows', 'cols', 'readonly', 'disabled',
                     'onkeyup', 'tabindex');
    }

    /**
     * Return the element as HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('textarea');
        $html = '<textarea id="' . $this->getHtmlId() . '" name="' . $this->getName() . '" '
                . $this->serialize($this->getHtmlAttributes()) . $this->_getUiId() . ' >';
        $html .= $this->getEscapedValue();
        $html .= "</textarea>";
        $html .= $this->getAfterElementHtml();
        return $html;
    }
}
