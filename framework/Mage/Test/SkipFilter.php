<?php

interface Mage_Test_SkipFilter
{
    /**
     * Filter test by name
     *
     * @abstract
     * @param string $name
     * @return bool
     */
    public function filter($name);
}
