<?php
declare(strict_types=1);

namespace Technoquill\Framework\Repository\Trait;

trait RepositoryMethods
{

    public function find($id) {
    }
    public function create(array $data) {
    }
    public function update($id, array $data): bool {
        return true;
    }
    public function delete($id): bool {
        return true;
    }

}