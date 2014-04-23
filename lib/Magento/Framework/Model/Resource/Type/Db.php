<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Model\Resource\Type;

abstract class Db extends \Magento\Framework\Model\Resource\Type\AbstractType
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_entityClass = 'Magento\Framework\Model\Resource\Entity\Table';
    }
}
