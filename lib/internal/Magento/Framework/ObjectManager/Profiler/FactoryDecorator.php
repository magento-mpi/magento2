<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager\Profiler;

class FactoryDecorator implements \Magento\Framework\ObjectManager\Factory
{
    /**
     * @var \Magento\Framework\ObjectManager\Factory
     */
    protected $subject;

    /**
     * @var Log
     */
    protected $log;

    /**
     * @param \Magento\Framework\ObjectManager\Factory $subject
     * @param Log $log
     */
    public function __construct(\Magento\Framework\ObjectManager\Factory $subject, Log $log)
    {
        $this->subject = $subject;
        $this->log = $log;
    }

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     *
     * @return void
     */
    public function setObjectManager(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->subject->setObjectManager($objectManager);
    }

    /**
     * {@inheritdoc}
     */
    public function create($requestedType, array $arguments = array())
    {
        $this->log->startCreating($requestedType);
        $result = $this->subject->create($requestedType, $arguments);
        $loggerClassName = get_class($result) . "\\Logger";
        $wrappedResult = new $loggerClassName($result, $this->log);
        $this->log->stopCreating($result);
        return $wrappedResult;
    }
}
