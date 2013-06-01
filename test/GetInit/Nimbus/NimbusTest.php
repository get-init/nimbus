<?php
/*
 * container
 * 
 * Copyright (c) 2012-2013, Ralf Fischer <themakii@gmail.com>.
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 
 *  * Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 
 * * Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in
 *    the documentation and/or other materials provided with the
 *    distribution.
 * 
 * * Neither the name of Ralf Fischer nor the names of his
 *    contributors may be used to endorse or promote products derived
 *    from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace GetInit\Nimbus;

use Basics\UnitTest;

/**
 *
 * @package    GetInit\Nimbus
 * @author     Ralf Fischer <themakii@gmail.com>
 * @copyright  2012-2013 Ralf Fischer <themakii@gmail.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://github.com/get-init/nimbus
 * @large
 */
class NimbusTest extends UnitTest
{
    public function testSettingSimpleClosureWorks()
    {
        $drop = function () {
            return 'bar';
        };

        $nimbus      = new Nimbus();
        $nimbus->foo = $drop;

        $this->assertAttributeContains($drop, 'drops', $nimbus);
    }

    public function testGettingPropertyWillInvokeSimpleClosure()
    {
        $value = 'bar';
        $drop  = function () use ($value) {
            return $value;
        };

        $nimbus      = new Nimbus();
        $nimbus->foo = $drop;

        $this->assertSame($value, $nimbus->foo);
    }

    public function testGettingPropertyWillPassInNimbusIfDependenciesAreNecessary()
    {
        $fooValue = 'foo';
        $fooDrop  = function () use ($fooValue) {
            return $fooValue;
        };

        $barValue   = 'bar';
        $fooBarDrop = function (Nimbus $n) use ($barValue) {
            $foo = $n->foo;
            return $foo . $barValue;
        };


        $nimbus         = new Nimbus();
        $nimbus->foo    = $fooDrop;
        $nimbus->foobar = $fooBarDrop;

        $actualFoobar = $nimbus->foobar;

        $this->assertSame($fooValue . $barValue, $actualFoobar);
    }

    public function testSettingClosureTwiceWillResultInException()
    {
        $nimbus      = new Nimbus();
        $nimbus->foo = function () {
            return 'foo';
        };

        $this->assertException(function () use ($nimbus) {
            $nimbus->foo = function () {
                return 'bar';
            };
        }, 'GetInit\Nimbus\DropAlreadyDeclaredException', '"foo"');
    }

    public function testReplacingDropWorksViaReplaceMethod()
    {
        $nimbus      = new Nimbus();
        $nimbus->foo = function () {
            return 'foo';
        };

        $replacementDrop = function () {
            return 'bar';
        };
        $nimbus->replace('foo', $replacementDrop);

        $this->assertAttributeContains($replacementDrop, 'drops', $nimbus);
        $this->assertSame('bar', $nimbus->foo);
    }

    public function testReplacingDropThrowsExceptionWhenNothingToReplaceThere()
    {
        $nimbus = new Nimbus();

        $this->assertException(function () use ($nimbus) {
            $nimbus->replace('foo', function () {
                return 'bar';
            });
        }, 'GetInit\Nimbus\DropNotPresentException', 'foo');

    }
}
