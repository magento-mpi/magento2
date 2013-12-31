<?php
/**
 * Test LockableLazyArrayClone
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Entity;

class LockableLazyArrayCloneTest extends \PHPUnit_Framework_TestCase
{

    /** @var LockableLazyArrayClone */
    private $_arrayAccess;

    public function setUp()
    {
        $this->_arrayAccess = new LockableLazyArrayClone();
    }

    public function testOffsetGetEmpty()
    {
        $result = $this->_arrayAccess->offsetGet('some_offset');

        $this->assertNull($result);
    }

    public function testOffsetSet()
    {
        $this->_arrayAccess->offsetSet('key', 'val');

        $this->assertEquals('val', $this->_arrayAccess->offsetGet('key'));
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage locked
     */
    public function testOffsetSetLocked()
    {
        $this->_arrayAccess->lock();

        $this->_arrayAccess->offsetSet('key', 'val');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage locked
     */
    public function testOffsetUnsetLocked()
    {
        $this->_arrayAccess->offsetSet('key', 'val');
        $this->_arrayAccess->lock();

        $this->_arrayAccess->offsetUnset('key');
    }

    public function testIsLockedWhenNotLocked()
    {
        $this->assertFalse($this->_arrayAccess->isLocked(), 'Should have returned false to indicate not locked.');
    }

    public function testIsLockedWhenLocked()
    {
        $this->_arrayAccess->lock();

        $this->assertTrue($this->_arrayAccess->isLocked(), 'Should have returned true to indicate locked.');
    }

    public function testCloneWillUnlock()
    {
        $this->_arrayAccess->offsetSet('key', 'val');
        $this->_arrayAccess->lock();
        $clone = clone $this->_arrayAccess;

        $this->assertEquals('val', $clone->offsetGet('key'));
        $this->assertFalse($clone->isLocked(), 'Should have returned false to indicate not locked.');

        $clone->offsetSet('key', 'new_val');
        $this->assertEquals('new_val', $clone->offsetGet('key'));

        $clone->offsetUnset('key');
        $this->assertFalse($clone->offsetExists('key'), 'Unset should have caused this to return false.');
    }

    public function testCloneCanLock()
    {
        $this->_arrayAccess->lock();
        $clone = clone $this->_arrayAccess;

        $clone->lock();

        $this->assertTrue($clone->isLocked(), 'Should have returned true to indicate locked.');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage locked
     */
    public function testArrayLocking()
    {
        $this->_arrayAccess->offsetSet('array', array());
        $this->_arrayAccess['array']['key'] = 'val';
        $this->assertEquals('val', $this->_arrayAccess['array']['key']);

        $this->_arrayAccess->lock();

        $this->_arrayAccess['array']['key'] = 'new_val';
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage locked
     */
    public function testRecursiveLocking()
    {
        $this->_arrayAccess['array'] = new LockableLazyArrayClone();
        $this->_arrayAccess['array']['key'] = 'val';
        $this->assertEquals('val', $this->_arrayAccess['array']['key']);

        $this->_arrayAccess->lock();

        $this->_arrayAccess['array']['key'] = 'new_val';
    }
}
