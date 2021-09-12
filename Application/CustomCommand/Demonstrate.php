<?php

namespace Application\CustomCommand;

use ConsoleCommandTool\Command\AbstractCommand;
use ConsoleCommandTool\Entity;

class Demonstrate extends AbstractCommand {

    private const DELIMETER = "\n\t- ";

    public function getName(): string {
        return 'demonstrate';
    }

    public function getDescription(): string {
        return "Show command name, arguments and options\n";
    }

    public function getOptions(): array {
        return [];
    }

    public function getArguments(): array {
        return [];
    }

    protected function _run(Entity\Input $input): Entity\Output {
        $arguments = [];
        $options = [];
        /** @var Entity\Argument|Entity\Option $value */
        foreach ($input->argumentValueByNames() as $name => $value) {
            if ($value instanceof Entity\Argument) {
                $arguments[] = $value->name();
            }
            if ($value instanceof Entity\Option) {
                $options[$value->name()] = $value->values(); 
            }
        }
        $commandName = $this->getName();
        $result = "\nCalled command: {$commandName}\n";
        if (count($arguments) > 0) {
            $argumentsList = implode(self::DELIMETER, $arguments);
            $result .= "\nArguments:" . self::DELIMETER . "{$argumentsList}\n";
        }
        if (count($options) > 0) {
            $result .= "\nOptions:";
            foreach ($options as $name => $values) {
                $optionsList = implode("\n\t\t- ", $values);
                $result .= self::DELIMETER . $name . "\n\t\t- {$optionsList}\n";
            }
        }
        return new Entity\Output($result);
    }
}