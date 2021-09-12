<?php

namespace ConsoleCommandTool\Entity;

class Output {

       private string $_message;

       public function __construct(string $message) {
           $this->_message = $message;
       }

       public function message(): string {
           return $this->_message;
       }
}