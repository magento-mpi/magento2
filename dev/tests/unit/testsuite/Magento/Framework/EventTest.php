<?php
/**
* {license_notice}
*
* @copyright   {copyright}
* @license     {license_link}
*/
namespace Magento\Framework;

use Magento\Framework\Event\Observer\Collection;
use Magento\Framework\Event\Observer;

/**
 * Class Event
 *
 * @package Magento\Framework
 */
class EventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Event
     */
    protected $event;

    /**
     * @var Collection
     */
    protected $observers;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $observer;

    public function setUp()
    {
        $data = [
            'name' => 'ObserverName',
            'block' => 'testBlockName'
        ];
        $this->event = new Event($data);
        $this->observers = new Collection();
        $this->observer = new Observer($data);
        $this->observers->addObserver($this->observer);
    }

    protected function tearDown()
    {
        unset($this->event);
    }

    public function testGetObservers()
    {
        $this->event->addObserver($this->observer);
        $expected = $this->observers;
        $result = $this->event->getObservers();
        $this->assertEquals($expected, $result);
    }

    public function testAddObservers()
    {
        $data = ['name' => 'Add New Observer'];
        $observer = new Observer($data);
        $this->event->addObserver($observer);
        $actual = $this->event->getObservers()->getObserverByName($data['name']);
        $this->assertSame($observer, $actual);
    }

    public function testRemoveObserverByName()
    {
        $data = [
            'name' => 'ObserverName',
        ];
        $this->event->addObserver($this->observer);
        $result = $this->event->getObservers()->getObserverByName($data['name']);
        $this->assertEquals($this->observer, $result);
    }

    public function testDispatch()
    {
        $this->assertInstanceOf('Magento\Framework\Event', $this->event->dispatch());
    }

    public function testGetName()
    {
        $data = 'ObserverName';
        $this->assertEquals($data, $this->event->getName());
        $this->event = new Event();
        $this->assertNull($this->event->getName());
    }

    public function testGetBlock()
    {
        $block = 'testBlockName';
        $this->assertEquals($block, $this->event->getBlock());
    }
} 
