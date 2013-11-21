<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Menu\Item;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\App\Helper\HelperFactory
     */
    protected $_helperFactory;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\App\Helper\HelperFactory $helperFactory
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\App\Helper\HelperFactory $helperFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_helperFactory = $helperFactory;
    }

    /**
     * Create menu item from array
     *
     * @param array $data
     * @return \Magento\Backend\Model\Menu\Item
     */
    public function create(array $data = array())
    {
        $module = 'Magento\Backend\Helper\Data';
        if (isset($data['module'])) {
            $module = $data['module'];
            unset($data['module']);
        }
        $data = array('data' => $data);
        $data['helper'] = $this->_helperFactory->get($module);
        return $this->_objectManager->create('Magento\Backend\Model\Menu\Item', $data);
    }
}
