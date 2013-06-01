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

class Nimbus
{
    /** @var array An array of closures producing objects */
    private $drops = array();

    /**
     * @param $name
     * @return mixed
     */
    function __get($name)
    {
        $closure = $this->drops[$name];

        return $closure($this);
    }

    public function __set($name, \Closure $closure)
    {
        $this->condenseDrop($name, $closure);
    }

    /**
     * @param string   $dropName
     * @param callable $closure
     * @throws DropAlreadyDeclaredException When the drop was already declared.
     */
    protected function condenseDrop($dropName, \Closure $closure)
    {
        if (isset($this->drops[$dropName])) {
            throw new DropAlreadyDeclaredException('Drop of name "' . $dropName . '" is already declared.');
        }
        $this->drops[$dropName] = $closure;
    }

    /**
     * @param string   $dropName
     * @param callable $replacementDrop
     * @throws DropNotPresentException
     */
    public function replace($dropName, $replacementDrop)
    {
        if (!isset($this->drops[$dropName])) {
            throw new DropNotPresentException(
                'Cannot replace a non-existent drop "'
                . $dropName
                . '" - configuration error?');
        }
        unset($this->drops[$dropName]);
        $this->condenseDrop($dropName, $replacementDrop);
    }
}