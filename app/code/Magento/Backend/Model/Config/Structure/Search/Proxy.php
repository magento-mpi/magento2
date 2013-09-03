<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_Search_Proxy
    implements Magento_Backend_Model_Config_Structure_SearchInterface
{
    /**
     * Object manager
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Backend_Model_Config_Structure
     */
    protected $_subject;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Retrieve subject
     *
     * @return Magento_Backend_Model_Config_Structure_SearchInterface
     */
    protected function _getSubject()
    {
        if (!$this->_subject) {
            $this->_subject = $this->_objectManager->get('Magento_Backend_Model_Config_Structure');
        }
        return $this->_subject;
    }

    /**
     * Find element by path
     *
     * @param string $path
     * @return Magento_Backend_Model_Config_Structure_ElementInterface|null
     */
    public function getElement($path)
    {
        return $this->_getSubject()->getElement($path);
    }
}
