<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Framework\Data\Form\Element;

use Magento\Framework\ObjectManager;

class Factory
{
    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * Standard library element types
     *
     * @var string[]
     */
    protected $_standardTypes = array(
        'button',
        'checkbox',
        'checkboxes',
        'column',
        'date',
        'editablemultiselect',
        'editor',
        'fieldset',
        'file',
        'gallery',
        'hidden',
        'image',
        'imagefile',
        'label',
        'link',
        'multiline',
        'multiselect',
        'note',
        'obscure',
        'password',
        'radio',
        'radios',
        'reset',
        'select',
        'submit',
        'text',
        'textarea',
        'time'
    );

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Factory method
     *
     * @param string $elementType Standard element type or Custom element class
     * @param array $config
     * @return AbstractElement
     * @throws \InvalidArgumentException
     */
    public function create($elementType, array $config = array())
    {
        if (in_array($elementType, $this->_standardTypes)) {
            $className = 'Magento\Framework\Data\Form\Element\\' . ucfirst($elementType);
        } else {
            $className = $elementType;
        }

        $element = $this->_objectManager->create($className, $config);
        if (!$element instanceof AbstractElement) {
            throw new \InvalidArgumentException(
                $className . ' doesn\'n extend \Magento\Framework\Data\Form\Element\AbstractElement'
            );
        }
        return $element;
    }
}
