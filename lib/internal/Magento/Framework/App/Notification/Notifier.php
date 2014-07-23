<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Notification;

class Notifier implements NotificationInterface
{

    /**
     * Add new message
     *
     * @param int $severity
     * @param string $title
     * @param string|string[] $description
     * @param string $url
     * @param bool $isInternal
     * @throws \Magento\Framework\Model\Exception
     * @return $this
     */
    public function add($severity, $title, $description, $url = '', $isInternal = true)
    {
        return $this;
    }

    /**
     * Add critical severity message
     *
     * @param string $title
     * @param string|string[] $description
     * @param string $url
     * @param bool $isInternal
     * @return $this
     */
    public function addCritical($title, $description, $url = '', $isInternal = true)
    {
        return $this;
    }

    /**
     * Add major severity message
     *
     * @param string $title
     * @param string|string[] $description
     * @param string $url
     * @param bool $isInternal
     * @return $this
     */
    public function addMajor($title, $description, $url = '', $isInternal = true)
    {
        return $this;
    }

    /**
     * Add minor severity message
     *
     * @param string $title
     * @param string|string[] $description
     * @param string $url
     * @param bool $isInternal
     * @return $this
     */
    public function addMinor($title, $description, $url = '', $isInternal = true)
    {
        return $this;
    }

    /**
     * Add notice
     *
     * @param string $title
     * @param string|string[] $description
     * @param string $url
     * @param bool $isInternal
     * @return $this
     */
    public function addNotice($title, $description, $url = '', $isInternal = true)
    {
        return $this;
    }
}
