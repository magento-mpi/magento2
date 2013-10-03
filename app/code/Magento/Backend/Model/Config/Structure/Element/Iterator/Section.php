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

class Section
    extends \Magento\Backend\Model\Config\Structure\Element\Iterator
{
    /**
     * @param \Magento\Backend\Model\Config\Structure\Element\Section $element
     */
    public function __construct(\Magento\Backend\Model\Config\Structure\Element\Section $element)
    {
        parent::__construct($element);
    }
}
