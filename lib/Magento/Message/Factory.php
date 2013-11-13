<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * Message model factory
 */
class Factory
{
    /**
     * Error type
     */
    const ERROR = 'error';

    /**
     * Warning type
     */
    const WARNING = 'warning';

    /**
     * Notice type
     */
    const NOTICE = 'notice';

    /**
     * Success type
     */
    const SUCCESS = 'success';

    /**
     * Allowed message types
     *
     * @var array
     */
    protected $types = array(
        self::ERROR,
        self::WARNING,
        self::NOTICE,
        self::SUCCESS,
    );

    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create message instance with specified parameters
     *
     * @param $type
     * @param string $code
     * @param string $class
     * @param string $method
     * @throws \InvalidArgumentException
     * @return \Magento\Message\AbstractMessage
     */
    public function create($type, $code = '', $class = '', $method = '')
    {
        if (!in_array($type, $this->types)) {
            throw new \InvalidArgumentException('Wrong message type');
        }

        $className = '\\Magento\\Message\\' . ucfirst($type);
        $message = $this->objectManager->create($className, array($code));
        if (!($message instanceof \Magento\Message\AbstractMessage)) {
            throw new \InvalidArgumentException($className . ' does\'nt extends \\Magento\\Message\\AbstractMessage');
        }

        $message->setClass($class);
        $message->setMethod($method);

        return $message;
    }

    /**
     * Create error message
     *
     * @param $code
     * @param string $class
     * @param string $method
     * @return \Magento\Message\AbstractMessage
     */
    public function error($code, $class='', $method='')
    {
        return $this->create(self::ERROR, $code, $class, $method);
    }

    /**
     * Create warning message
     *
     * @param $code
     * @param string $class
     * @param string $method
     * @return \Magento\Message\AbstractMessage
     */
    public function warning($code, $class='', $method='')
    {
        return $this->create(self::WARNING, $code, $class, $method);
    }

    /**
     * Create notice message
     *
     * @param $code
     * @param string $class
     * @param string $method
     * @return \Magento\Message\AbstractMessage
     */
    public function notice($code, $class='', $method='')
    {
        return $this->create(self::NOTICE, $code, $class, $method);
    }

    /**
     * Create success message
     *
     * @param $code
     * @param string $class
     * @param string $method
     * @return \Magento\Message\AbstractMessage
     */
    public function success($code, $class='', $method='')
    {
        return $this->create(self::SUCCESS, $code, $class, $method);
    }
}
