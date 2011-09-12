<?php

/**
 * Class that violates the allowed method number
 */
abstract class Foo
{
    private function method01()
    {
        return 'something';
    }

    protected function method02()
    {
        /* Use private method to not consider it unused */
        $this->method01();
    }

    abstract protected function method03();

    abstract protected function method04();

    abstract protected function method05();

    abstract protected function method06();

    abstract protected function method07();

    abstract protected function method08();

    abstract protected function method09();

    abstract protected function method10();

    abstract public function method11();
}
