<?php

namespace ConsoleCommandTool\Entity;

class Argument {

    private string $_name;

    public function __construct(string $name) {
        $this->_name = $name;
    }

    public function name(): string {
        return $this->_name;
    }

}