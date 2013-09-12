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
 * @category   Magento
 * @package    Magento_Data
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Data_Form_Element_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Standard library element types
     *
     * @var array
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
        'time',
    );

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Factory method
     *
     * @param string $elementType Standard element type or Custom element class
     * @param array $config
     * @return Magento_Data_Form_Element_Abstract
     * @throws InvalidArgumentException
     */
    public function create($elementType, array $config = array())
    {
        if (in_array($elementType, $this->_standardTypes)) {
            $className = 'Magento_Data_Form_Element_' . ucfirst($elementType);
        } else {
            $className = $elementType;
        }

        $element = $this->_objectManager->create($className, $config);
        if (!($element instanceof Magento_Data_Form_Element_Abstract)) {
            throw new InvalidArgumentException($className . ' doesn\'n extend Magento_Data_Form_Element_Abstract');
        }
        return $element;
    }
}
