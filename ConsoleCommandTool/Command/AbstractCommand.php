<?php

namespace ConsoleCommandTool\Command;

use ConsoleCommandTool\Entity;

abstract class AbstractCommand {

    private const ARG_HELP = 'help';

    abstract public function getName(): string;

    abstract public function getDescription(): string;

    /**
     * @return Entity\Option[];
     */
    abstract public function getOptions(): array;

    /**
     * @return Entity\Argument[]
     */
    abstract public function getArguments(): array;

    final public function execute(Entity\Input $input): Entity\Output {
        if ($input->hasArgument(self::ARG_HELP)) {
            return new Entity\Output($this->getDescription());
        }
        return $this->_run($input);
    }

    abstract protected function _run(Entity\Input $input): Entity\Output;

}