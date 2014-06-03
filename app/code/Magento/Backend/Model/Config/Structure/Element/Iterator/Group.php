<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Structure\Element\Iterator;

class Group extends \Magento\Backend\Model\Config\Structure\Element\Iterator
{
    /**
     * @param \Magento\Backend\Model\Config\Structure\Element\Group $element
     */
    public function __construct(\Magento\Backend\Model\Config\Structure\Element\Group $element)
    {
        parent::__construct($element);
    }
}
