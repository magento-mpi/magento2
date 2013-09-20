<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * EAV form object factory
 */
class Magento_Eav_Model_Form_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new form object
     *
     * @param string $form
     * @param array $data
     * @throws InvalidArgumentException
     * @return Magento_Eav_Model_Form
     */
    public function create($form, array $data = array())
    {
        $formInstance = $this->_objectManager->create($form, $data);
        if (false == ($formInstance instanceof Magento_Eav_Model_Form)) {
            throw new InvalidArgumentException(
                $form . ' is not instance of Magento_Eav_Model_Form'
            );
        }
        return $formInstance;
    }
}
