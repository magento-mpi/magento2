<?php

namespace Smart\Suite\Test;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

class FixtureRepository implements \Iterator
{
    /**
     * @var
     */
    protected $_key;

    /**
     * @var
     */
    protected $_current;

    /**
     * @var array
     */
    protected $_fixtures = array();

    public function rewind()
    {
        $this->_fixtures = $this->collectFixtures();

        if (count($this->_fixtures)) {
            $this->_current = reset($this->_fixtures);
            $this->_key = key($this->_fixtures);
        }
    }

    public function valid()
    {
        return array_key_exists($this->_key, $this->_fixtures);
    }

    public function key()
    {
        return $this->_key;
    }

    /**
     * @return DataFixture
     */
    public function current()
    {
        return $this->_current;
    }

    public function next()
    {
        $this->_current = next($this->_fixtures);
        $this->_key = key($this->_fixtures);

        if (false === $this->_current) {
            $this->_key = null;
            $this->_current = null;
            return;
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @return array
     */
    protected function collectFixtures()
    {
        $items = array();

        $fixtureFactory = Factory::getFixtureFactory();

        $reflectionClass = new \ReflectionClass('\\Mtf\\Fixture\\FixtureFactory');

        foreach ($reflectionClass->getMethods() as $method) {
            if ($method->isPublic() && !$method->isConstructor()) {
                $methodName = $method->getName();
                $items[$methodName] = $fixtureFactory->$methodName();
            }
        }

        return $items;
    }
}
