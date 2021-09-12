<?php

namespace ConsoleCommandTool;

use Exception;
use ConsoleCommandTool\Command\AbstractCommand;
use ConsoleCommandTool\Service\Parser;
use ConsoleCommandTool\Entity;

class Cli {

    private const COMMAND_LIST_COMMAND = 'command_list';

    /**
     * @var Parser
     */
    protected $_parser;

    /**
     * @var AbstractCommand[]
     */
    protected $_commandsByName = [];

    public function __construct() {
        $this->_parser = new Parser();
    }

    final public function addCommand(AbstractCommand $command): void {
        try {
            $this->_validateCommand($command);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        $this->_commandsByName[$command->getName()] = $command;
    }

    public function run(): void {
        $commandName = $this->_getCommandName();
        var_dump($commandName);
        if (array_key_exists($commandName, $this->_commandsByName)) {
            var_dump('test');
            global $argv;
            $requestParams = $argv;
            unset($requestParams[0], $requestParams[1]);
            $requestParams = $this->_parser->parse($requestParams);
            $result = $this->_runCommand($this->_commandsByName[$commandName], $requestParams);
            echo $result->message();
        } elseif ($commandName === self::COMMAND_LIST_COMMAND) {
            foreach ($this->_commandsByName as $name => $command) {
                $description = $command->getDescription();
                echo "- {$name}\n\t- {$description}\n";
            }
        } else {
            echo "Command \"{$commandName}\" doesn't exists\n";
        }
    }

    final protected function _runCommand(AbstractCommand $command, array $requestParams): Entity\Output {
        try {
            $input = $this->_createInput($requestParams);
        } catch (Exception $e) {
            return new Entity\Output($e->getMessage());
        }
        return $command->execute($input);
    }

    /**
     * @param mixed $requestParams
     * @return Entity\Input
     */
    private function _createInput(array $requestParams): Entity\Input {
        $result = [];
        foreach ($requestParams as $requestParam) {
            if ($requestParam instanceof Entity\Argument) {
                $result[$requestParam->name()] = $requestParam;
            } elseif ($requestParam instanceof Entity\Option) {
                $result[$requestParam->name()] = $requestParam;
            }
        }
        return new Entity\Input($result);
    }

    private function _getCommandName(): string {
        global $argv;
        return array_key_exists(1, $argv) ? $argv[1] : self::COMMAND_LIST_COMMAND;
    }

    private function _validateCommand(AbstractCommand $command): void {
        $this->_validateCommandName($command);
        $this->_validateCommandArguments($command);
    }

    /**
     * @param AbstractCommand $command
     * @throws Exception
     */
    private function _validateCommandName(AbstractCommand $command): void {
        $commandName = $command->getName();
        if (array_key_exists($commandName, $this->_commandsByName)) {
            throw new Exception("Command {$commandName} already exists\n");
        }
    }

    /**
     * @param AbstractCommand $command
     * @throws Exception
     */
    private function _validateCommandArguments(AbstractCommand $command): void {
        $argumentNames = [];
        foreach ($command->getArguments() as $argument) {
            $argumentName = $argument->name();
            if (in_array($argumentName, $argumentNames)) {
                throw new Exception("Argument name \"{$argumentName}\" must be unique\n");
            }
            $argumentNames[] = $argumentName;
        }

        $optionNames = [];
        foreach ($command->getOptions() as $option) {
            $optiontName = $option->name();
            if (in_array($optiontName, $optionNames)) {
                throw new Exception("Option name \"{$argumentName}\" must be unique\n");
            }
            $optionNames[] = $optiontName;
            $this->_validateOptionValues($option);
        }
    }

    /**
     * @param Entity\Option $option
     * @throws Exception
     */
    private function _validateOptionValues(Entity\Option $option): void {
        $values = $option->values();
        $optionName = $option->name();
        if (count($values) === 0) {
            throw new Exception("Option \"{$optionName}\" must have at least one value\n");
        }

        $valueNames = [];
        foreach ($values as $value) {
            if (in_array($value, $valueNames)) {
                throw new Exception("Value name \"{$value}\" for option \"{$optionName}\" must be unique\n");
            }
            $valueNames[] = $value;
        }
    }

}