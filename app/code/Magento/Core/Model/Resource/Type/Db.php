<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Resource\Type;

abstract class Db extends \Magento\Core\Model\Resource\Type\AbstractType
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_entityClass = '\Magento\Core\Model\Resource\Entity\Table';
    }
}
