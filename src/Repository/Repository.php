<?php
declare(strict_types=1);

namespace Technoquill\Framework\Repository;

use Technoquill\Framework\Contract\RepositoryInterface;
use Technoquill\Framework\Repository\Trait\RepositoryMethods;

abstract class Repository implements RepositoryInterface
{

    use RepositoryMethods;



}