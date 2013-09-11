<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Url handler factory
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Layout\Argument\Handler;

class UrlFactory
    implements \Magento\Core\Model\Layout\Argument\HandlerFactoryInterface
{
    /**
     * Create url type handler
     * @return \Magento\Core\Model\Layout\Argument\HandlerInterface
     */
    public function createHandler()
    {
        return \Mage::getModel('\Magento\Core\Model\Layout\Argument\Handler\Url', array(
            'urlModel' => \Mage::app()->getStore()->getUrlModel()
        ));
    }
}
