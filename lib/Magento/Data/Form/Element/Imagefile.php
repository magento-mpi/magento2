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
 * Form image file element
 *
 * @category   Magento
 * @package    Magento_Data
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Data\Form\Element;

class Imagefile extends \Magento\Data\Form\Element\AbstractElement
{
    /**
     * @param \Magento\Escaper $escaper
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param array $attributes
     */
    public function __construct(
        \Magento\Escaper $escaper,
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        $attributes = array()
    ) {
        parent::__construct($escaper, $factoryElement, $factoryCollection, $attributes);
        $this->setType('file');
        $this->setExtType('imagefile');
        $this->setAutosubmit(false);
        $this->setData('autoSubmit', false);
    }
}
