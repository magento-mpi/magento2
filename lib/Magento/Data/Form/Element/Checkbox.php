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
 * Form checkbox element
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Form\Element;

use Magento\Data\Form\Element\CollectionFactory;
use Magento\Data\Form\Element\Factory;
use Magento\Escaper;

class Checkbox extends \Magento\Data\Form\Element\AbstractElement
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
        $this->setType('checkbox');
        $this->setExtType('checkbox');
    }

    /**
     * @return string[]
     */
    public function getHtmlAttributes()
    {
        return array('type', 'title', 'class', 'style', 'checked', 'onclick', 'onchange', 'disabled', 'tabindex');
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        if ($checked = $this->getChecked()) {
            $this->setData('checked', true);
        }
        else {
            $this->unsetData('checked');
        }
        return parent::getElementHtml();
    }

    /**
     * Set check status of checkbox
     *
     * @param boolean $value
     * @return \Magento\Data\Form\Element\Checkbox
     */
    public function setIsChecked($value=false)
    {
        $this->setData('checked', $value);
        return $this;
    }

    /**
     * Return check status of checkbox
     *
     * @return boolean
     */
    public function getIsChecked() {
        return $this->getData('checked');
    }
}
