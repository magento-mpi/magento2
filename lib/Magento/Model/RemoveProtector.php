<?php
/**
 * Abstract model context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Model;

class RemoveProtector implements RemoveProtectorInterface
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
    public function canBeRemoved(AbstractModel $model)
    {
        $canBeRemoved = true;

        if ($this->registry->registry('isSecureArea')) {
            $canBeRemoved = true;
        } elseif (in_array($this->getBaseClassName($model), $this->protectedModels)) {
            $canBeRemoved = false;
        }

        return $canBeRemoved;
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
