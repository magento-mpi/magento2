<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Notification;

/*
 * List of registered system notifiers
 */
class NotifierList
{
    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * List of notifiers
     *
     * @var NotifierInterface[]|string[]
     */
    protected $notifiers;

    /**
     * Whether the list of notifiers is verified (all notifiers should implement NotifierInterface  interface)
     *
     * @var bool
     */
    protected $isNotifiersVerified;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     * @param NotifierInterface[]|string[] $notifiers
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager, $notifiers = array())
    {
        $this->objectManager = $objectManager;
        $this->notifiers = $notifiers;
        $this->isNotifiersVerified = false;
    }

    /**
     * Returning list of notifiers.
     *
     * @return NotifierInterface[]
     * @throws \InvalidArgumentException
     */
    public function asArray()
    {
        if (!$this->isNotifiersVerified) {
            $hasErrors = false;
            foreach ($this->notifiers as $classIndex => $class) {
                $notifier = $this->objectManager->get($class);
                if (($notifier instanceof NotifierInterface)) {
                    $this->notifiers[$classIndex] = $notifier;
                } else {
                    $hasErrors = true;
                    unset($this->notifiers[$classIndex]);
                }
            }
            $this->isNotifiersVerified = true;
            if ($hasErrors) {
                throw new \InvalidArgumentException('All notifiers should implements NotifierInterface');
            }
        }
        return $this->notifiers;
    }
}
