<?php
declare(strict_types=1);

namespace Technoquill\Framework\Contract;

interface RepositoryInterface
{

    public function find($id); //: ?EntityInterface;
    public function create(array $data); //: EntityInterface;
    public function update($id, array $data): bool;
    public function delete($id): bool;
}