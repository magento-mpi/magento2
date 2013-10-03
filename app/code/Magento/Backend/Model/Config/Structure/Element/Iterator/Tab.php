<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Structure\Element\Iterator;

class Tab
    extends \Magento\Backend\Model\Config\Structure\Element\Iterator
{
    /**
     * @param \Magento\Backend\Model\Config\Structure\Element\Tab $element
     */
    public function __construct(\Magento\Backend\Model\Config\Structure\Element\Tab $element)
    {
        parent::__construct($element);
    }
}
