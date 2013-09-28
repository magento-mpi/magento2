<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Eav\Model\Form;

/**
 * EAV form object factory
 */
class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new form object
     *
     * @param string $form
     * @param array $data
     * @throws \InvalidArgumentException
     * @return \Magento\Eav\Model\Form
     */
    public function create($form, array $data = array())
    {
        $formInstance = $this->_objectManager->create($form, $data);
        if (false == ($formInstance instanceof \Magento\Eav\Model\Form)) {
            throw new \InvalidArgumentException(
                $form . ' is not instance of \Magento\Eav\Model\Form'
            );
        }
        return $formInstance;
    }
}
