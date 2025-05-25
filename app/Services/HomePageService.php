<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\HomePageRepository;

class HomePageService
{

    public HomePageRepository $repository;


    public function __construct(HomePageRepository $repository)
    {
        $this->repository = $repository;
    }


    public function create()
    {
        return $this->repository->create([]);
    }
}