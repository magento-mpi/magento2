<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * \Magento\Message\Collection test case
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Message\Collection
     */
    protected $model;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $this->objectManager->getObject('Magento\Message\Collection');
    }

    /**
     * @cover \Magento\Message\Collection::addMessage
     * @cover \Magento\Message\Collection::getItemsByType
     */
    public function testAddMessage()
    {
        $messages = array(
            $this->objectManager->getObject('Magento\Message\Error'),
            $this->objectManager->getObject('Magento\Message\Error'),
            $this->objectManager->getObject('Magento\Message\Error'),
        );

        foreach ($messages as $message) {
            $this->model->addMessage($message);
        }

        $this->assertEquals($messages, $this->model->getItemsByType(MessageInterface::TYPE_ERROR));
        $this->assertEmpty($this->model->getItemsByType(MessageInterface::TYPE_SUCCESS));
        $this->assertEmpty($this->model->getItemsByType(MessageInterface::TYPE_NOTICE));
        $this->assertEmpty($this->model->getItemsByType(MessageInterface::TYPE_WARNING));
    }

    /**
     * @cover \Magento\Message\Collection::addMessage
     * @cover \Magento\Message\Collection::getItems
     * @cover \Magento\Message\Collection::getLastAddedMessage
     */
    public function testGetItems()
    {
        $messages = array(
            $this->objectManager->getObject('Magento\Message\Error'),
            $this->objectManager->getObject('Magento\Message\Notice'),
            $this->objectManager->getObject('Magento\Message\Notice'),
            $this->objectManager->getObject('Magento\Message\Warning'),
            $this->objectManager->getObject('Magento\Message\Warning'),
            $this->objectManager->getObject('Magento\Message\Success')
        );

        foreach ($messages as $message) {
            $this->model->addMessage($message);
        }

        $this->assertEquals($messages, $this->model->getItems());
        $this->assertEquals(end($messages), $this->model->getLastAddedMessage());
    }

    /**
     * @cover \Magento\Message\Collection::addMessage
     * @cover \Magento\Message\Collection::getItemsByType
     * @cover \Magento\Message\Collection::getCount
     * @cover \Magento\Message\Collection::getCountByType
     */
    public function testGetItemsByType()
    {
        $messages = array(
            $this->objectManager->getObject('Magento\Message\Error'),
            $this->objectManager->getObject('Magento\Message\Notice'),
            $this->objectManager->getObject('Magento\Message\Success'),
            $this->objectManager->getObject('Magento\Message\Notice'),
            $this->objectManager->getObject('Magento\Message\Success'),
            $this->objectManager->getObject('Magento\Message\Warning'),
            $this->objectManager->getObject('Magento\Message\Error')
        );

        $messageTypes = array(
            MessageInterface::TYPE_SUCCESS => 2,
            MessageInterface::TYPE_NOTICE => 2,
            MessageInterface::TYPE_WARNING => 1,
            MessageInterface::TYPE_ERROR => 2
        );

        foreach ($messages as $message) {
            $this->model->addMessage($message);
        }

        $this->assertEquals(count($messages), $this->model->getCount());

        foreach ($messageTypes as $type => $count) {
            $messagesByType = $this->model->getItemsByType($type);
            $this->assertEquals($count, $this->model->getCountByType($type));
            $this->assertEquals($count, count($messagesByType));

            /** @var MessageInterface $message */
            foreach ($messagesByType as $message) {
                $this->assertEquals($type, $message->getType());
            }
        }
    }

    /**
     * @cover \Magento\Message\Collection::addMessage
     * @cover \Magento\Message\Collection::getErrors
     */
    public function testGetErrors()
    {
        $messages = array(
            $this->objectManager->getObject('Magento\Message\Error'),
            $this->objectManager->getObject('Magento\Message\Notice'),
            $this->objectManager->getObject('Magento\Message\Error'),
            $this->objectManager->getObject('Magento\Message\Error'),
            $this->objectManager->getObject('Magento\Message\Warning'),
            $this->objectManager->getObject('Magento\Message\Error')
        );

        foreach ($messages as $message) {
            $this->model->addMessage($message);
        }

        $this->assertEquals($this->model->getItemsByType(MessageInterface::TYPE_ERROR), $this->model->getErrors());
        $this->assertEquals(4, count($this->model->getErrors()));
    }

    /**
     * @cover \Magento\Message\Collection::getMessageByIdentifier
     * @cover \Magento\Message\Collection::deleteMessageByIdentifier
     */
    public function testGetMessageByIdentifier()
    {
        $messages = array(
            $this->objectManager->getObject('Magento\Message\Error')->setIdentifier('error_id'),
            $this->objectManager->getObject('Magento\Message\Notice')->setIdentifier('notice_id'),
            $this->objectManager->getObject('Magento\Message\Warning')->setIdentifier('warning_id'),
        );

        foreach ($messages as $message) {
            $this->model->addMessage($message);
        }

        $message = $this->model->getMessageByIdentifier('notice_id');
        $this->assertEquals(MessageInterface::TYPE_NOTICE, $message->getType());
        $this->assertEquals('notice_id', $message->getIdentifier());

        $this->assertEquals(count($messages), $this->model->getCount());
        $this->model->deleteMessageByIdentifier('notice_id');
        $this->assertEquals((count($messages) - 1), $this->model->getCount());

        $this->assertEmpty($this->model->getMessageByIdentifier('notice_id'));
    }

    /**
     * @cover \Magento\Message\Collection::clear
     */
    public function testClear()
    {
        $messages = array(
            $this->objectManager->getObject('Magento\Message\Error'),
            $this->objectManager->getObject('Magento\Message\Warning'),
            $this->objectManager->getObject('Magento\Message\Notice'),
            $this->objectManager->getObject('Magento\Message\Success')
        );

        foreach ($messages as $message) {
            $this->model->addMessage($message);
        }

        $this->assertEquals(count($messages), $this->model->getCount());
        $this->model->clear();
        $this->assertEmpty($this->model->getCount());
    }

    /**
     * @cover \Magento\Message\Collection::clear
     */
    public function testClearWithSticky()
    {
        $messages = array(
            $this->objectManager->getObject('Magento\Message\Error'),
            $this->objectManager->getObject('Magento\Message\Warning'),
            $this->objectManager->getObject('Magento\Message\Notice')->setIsSticky(true),
            $this->objectManager->getObject('Magento\Message\Success')
        );

        foreach ($messages as $message) {
            $this->model->addMessage($message);
        }

        $this->assertEquals(count($messages), $this->model->getCount());
        $this->model->clear();
        $this->assertEquals(1, $this->model->getCount());
    }
}
