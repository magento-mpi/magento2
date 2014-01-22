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
 * Form text element
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Form\Element;

use Magento\Data\Form\Element\CollectionFactory;
use Magento\Data\Form\Element\Factory;
use Magento\Escaper;

class Text extends \Magento\Data\Form\Element\AbstractElement
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
        $this->setType('text');
        $this->setExtType('textfield');
    }

    /**
     * Get the HTML
     *
     * @return mixed
     */
    public function getHtml()
    {
        $this->addClass('input-text');
        return parent::getHtml();
    }

    /**
     * Get the attributes
     *
     * @return string[]
     */
    public function getHtmlAttributes()
    {
        return array('type', 'title', 'class', 'style', 'onclick', 'onchange', 'onkeyup', 'disabled', 'readonly',
            'maxlength', 'tabindex', 'placeholder');
    }
}
