<?php

class Mage_Test_Regexp implements Mage_Test_SkipFilter
{
    /**
     * Regexp string list
     *
     * @var array
     */
    protected $_regexpList = array();

    /**
     * Constructor
     *
     * @param string|array $regexp
     */
    public function __construct($regexp)
    {
        $this->_regexpList = (array)$regexp;
    }

    /**
     * Filter test by regexp
     *
     * @param string $name
     * @return bool
     */
    public function filter($name)
    {
        foreach ($this->_regexpList as $regexp) {
            if (preg_match($regexp, $name)) {
                return true;
            }
        }
        return false;
    }
}