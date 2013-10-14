<?php

/**
 * {license_notice}
 *
 * @api
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Smart\Suite\Test;

use Mtf\Fixture\DataFixture;
use Mtf\Util\FixtureIterator;
use Smart\Suite\Test\FixtureRepository;

class EntityIterator implements \Iterator
{
    /**
     * @var DataFixture
     */
    protected $_currentFixture;

    /**
     * @var FixtureIterator
     */
    protected $_currentFixtureIterator;

    /**
     * @var string
     */
    protected $_key;

    /**
     * @var
     */
    protected $_current;

    /**
     * @var FixtureRepository
     */
    protected $_fixtureRepository;

    public function rewind()
    {
        $this->_fixtureRepository = new FixtureRepository();
        $this->_fixtureRepository->rewind();

        if ($this->_fixtureRepository->valid()) {
            $this->_currentFixture = $this->_fixtureRepository->current();

            $this->_currentFixtureIterator = new FixtureIterator($this->_currentFixture);
            $this->_currentFixtureIterator->rewind();

            if ($this->_currentFixtureIterator->valid()) {
                $current = $this->_currentFixtureIterator->current();
                $this->_current = reset($current);
                $this->_key = key($current);
            } else {
                $this->_currentFixtureIterator = null;
                $this->_currentFixture = null;
                $this->_current = null;
                $this->_key = null;

                $this->next();
            }
        }
    }

    public function valid()
    {
        return (null !== $this->_key);
    }

    public function key()
    {
        return $this->_key;
    }

    public function current()
    {
        return array($this->_current);
    }

    public function next()
    {
        if (null === $this->_currentFixtureIterator) {
            $this->_fixtureRepository->next();
            if ($this->_fixtureRepository->valid()) {
                $this->_currentFixture = $this->_fixtureRepository->current();

                $this->_currentFixtureIterator = new FixtureIterator($this->_currentFixture);
                $this->_currentFixtureIterator->rewind();

                if ($this->_currentFixtureIterator->valid()) {
                    $current = $this->_currentFixtureIterator->current();
                    $this->_current = reset($current);
                    $this->_key = key($current);
                } else {
                    $this->_currentFixtureIterator = null;
                    $this->_currentFixture = null;
                    $this->_current = null;
                    $this->_key = null;

                    $this->next();
                }
            }
        } else {
            $this->_currentFixtureIterator->next();

            if ($this->_currentFixtureIterator->valid()) {
                $current = $this->_currentFixtureIterator->current();
                $this->_current = reset($current);
                $this->_key = key($current);
            } else {
                $this->_currentFixtureIterator = null;
                $this->_currentFixture = null;
                $this->_current = null;
                $this->_key = null;

                $this->next();
            }
        }
    }
}
