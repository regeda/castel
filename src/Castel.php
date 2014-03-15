<?php

/**
 * Copyright (c) 2014 Anthony Regeda
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Castel main class.
 *
 * @package castel
 * @author Anthony Regeda
 */
class Castel
{
    private $values;

    /**
     * Instantiate the container
     *
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        $this->values = $values;
    }

    /**
     * Sets a parameter or an object
     *
     * @param string $id
     * @param mixed $value
     * @return \Castel
     * @throws InvalidArgumentException
     */
    public function share($id, $value)
    {
        if (property_exists($this, $id)) {
            throw new InvalidArgumentException(sprintf('Identifier "%s" is already mutated', $id));
        }
        $this->values[$id] = $value;
        return $this;
    }

    /**
     * Extends an object definition
     *
     * @param string $id
     * @param Closure $callable
     * @return \Castel
     * @throws InvalidArgumentException
     */
    public function extend($id, Closure $callable)
    {
        if (property_exists($this, $id)) {
            return $this->$id = $callable($this->$id, $this);
        }
        if (!isset($this->values[$id])) {
            throw new InvalidArgumentException(sprintf('Identifier "%s" is undefined.', $id));
        }
        $parent = $this->values[$id];
        $this->values[$id] = function ($that) use ($callable, $parent) {
            return $callable($this->mutate($parent), $that);
        };
        return $this;
    }

    /**
     * Gets a parameter or an object
     *
     * @param string $id
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function __get($id)
    {
        if (!isset($this->values[$id])) {
            throw new InvalidArgumentException(sprintf('Identifier "%s" is undefined.', $id));
        }
        return $this->$id = $this->mutate($this->values[$id]);
    }

    /**
     * Tries to invoke a value
     *
     * @param mixed $value
     * @return mixed
     */
    private function &mutate(&$value)
    {
        if (is_callable($value)) {
            $value = $value($this);
        }
        return $value;
    }
}
