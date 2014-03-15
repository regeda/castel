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

namespace Castel\Tests;

use \Castel;

class CastelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Identifier "something" is undefined.
     */
    public function testUndefinedIdentifier()
    {
        $castel = new Castel();
        $castel->something;
    }

    public function provideBarSharing()
    {
        return [
            ['bar'],
            [function() { return 'bar';}],
            [new Invokable('bar')]
        ];
    }

    /**
     * @dataProvider provideBarSharing
     * @param mixed $bar
     */
    public function testShare($bar)
    {
        $castel = new Castel();
        $castel->share('foo', $bar);
        $this->assertSame('bar', $castel->foo);
    }

    public function testMutate()
    {
        $invokable = $this->getMock('\Castel\Tests\Invokable');

        $invokable
            ->expects($this->once())
            ->method('__invoke')
            ->will($this->returnCallback(function () {
                return uniqid();
            }));

        $castel = new Castel();
        $castel->share('uniqid', $invokable);

        $this->assertSame($castel->uniqid, $castel->uniqid);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Identifier "something" is undefined.
     */
    public function testUndefinedIdentifierOnExtend()
    {
        $castel = new Castel();
        $castel->extend('something', function () {});
    }

    public function testExtendBeforeMutation()
    {
        $castel = new Castel();
        $castel->share('foo', 'Hello');
        $castel->extend('foo', function ($value) {
            return $value.' World!';
        });
        $this->assertSame('Hello World!', $castel->foo);
    }

    public function testExtendAfterMutation()
    {
        $castel = new Castel();
        $castel->share('foo', 'Hello');
        $castel->foo; // <---- mutation
        $castel->extend('foo', function ($value) {
            return $value.' Planet!';
        });
        $this->assertSame('Hello Planet!', $castel->foo);
    }

    public function testDoubleExtend()
    {
        $castel = new Castel();
        $castel->share('foo', 'To be');
        $castel->extend('foo', function ($value) {
            return $value.' or ';
        });
        $castel->extend('foo', function ($value) {
            return $value.'not to be!';
        });
        $this->assertSame('To be or not to be!', $castel->foo);
    }

    public function testNewShareAfterMutation()
    {
        $castel = new Castel();
        $castel->share('foo', 'bar');
        $castel->foo; // <---- mutation
        $castel->share('foo', 'baz');
        $this->assertSame('bar', $castel->foo);
    }
}
