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
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
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
        if (false == $formInstance instanceof \Magento\Eav\Model\Form) {
            throw new \InvalidArgumentException($form . ' is not instance of \Magento\Eav\Model\Form');
        }
        return $formInstance;
    }
}
