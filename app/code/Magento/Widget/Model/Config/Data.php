<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Widget\Model\Config;

class Data extends \Magento\Config\Data\Scoped
{
    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global', 'design');

}
