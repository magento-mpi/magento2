<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Model\Resource\Type;

abstract class Db extends \Magento\Model\Resource\Type\AbstractType
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_entityClass = 'Magento\Model\Resource\Entity\Table';
    }
}
