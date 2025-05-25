<?php
declare(strict_types=1);

namespace Technoquill\Framework\Controller;

use Technoquill\Framework\Contract\ControllerInterface;

class ConsoleController implements ControllerInterface
{

    /** @var array  */
    protected array $arguments = [];

    /**
     * Sets the arguments for the instance.
     *
     * @param array $arguments The arguments to set.
     * @return void
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

}