<?php
/**
 * Action validator, remove action
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Model\ActionValidator;

use Magento\Model\AbstractModel;

class RemoveAction
{
    /**
     * @var \Magento\Registry
     */
    protected $registry;

    /**
     * @var array
     */
    protected $protectedModels;

    /**
     * @param \Magento\Registry $registry
     * @param array $protectedModels
     */
    public function __construct(\Magento\Registry $registry, array $protectedModels = array())
    {
        $this->registry = $registry;
        $this->protectedModels = $protectedModels;
    }

    /**
     * Safeguard function that checks if item can be removed
     *
     * @param AbstractModel $model
     * @return bool
     */
    public function isAllowed(AbstractModel $model)
    {
        $isAllowed = true;

        if ($this->registry->registry('isSecureArea')) {
            $isAllowed = true;
        } elseif (in_array($this->getBaseClassName($model), $this->protectedModels)) {
            $isAllowed = false;
        }

        return $isAllowed;
    }

    /**
     * Get clean model name without Interceptor and Proxy part and slashes
     * @param object $object
     * @return mixed
     */
    protected function getBaseClassName($object)
    {
        $className = ltrim(get_class($object), "\\");
        $className = str_replace(array('\Interceptor', '\Proxy'), array(''), $className);

        return $className;
    }
}