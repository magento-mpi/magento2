<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Full page cache cookie model
 *
 * @category   Enterprise
 * @package    Enterprise_PageCache
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PageCache_Model_CookieFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param array $arguments
     * @return Enterprise_PageCache_Model_Cookie
     */
    public function get(array $arguments = array())
    {
        return $this->_objectManager->get('Enterprise_PageCache_Model_Cookie', $arguments);
    }
}
