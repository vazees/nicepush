<?php

namespace ConsoleCommandTool\Entity;

class Input {

    /**
     * @var array
     */
    private array $_argumentValuesByName;

    /**
     * @param array $argumentValuesByName
     */
    public function __construct(array $argumentValuesByName) {
        $this->_argumentValuesByName = $argumentValuesByName;
    }

    /**
     * @return array
     */
    public function argumentValueByNames(): array {
        return $this->_argumentValuesByName;
    }

    /**
     * @param string $argumentName
     * @return mixed
     * @throws \Exception
     */
    public function getArgumentValueByName(string $argumentName) {
        if (!$this->hasArgument($argumentName)) {
            throw new \Exception("Param '{$argumentName}' did not exists");
        }
        return $this->_argumentValuesByName[$argumentName];
    }

    public function hasArgument(string $argumentName): bool {
        return array_key_exists($argumentName, $this->_argumentValuesByName) && $this->_argumentValuesByName[$argumentName] !== null;
    }
}