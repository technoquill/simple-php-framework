<?php
declare(strict_types=1);

namespace App\Http\Controllers;


use App\Services\HomePageService;
use Technoquill\Framework\Controller\WebController;
use Technoquill\Framework\Http\Response;

class HomeController extends WebController
{


    /**
     * Handles the incoming request and returns the rendered home page response.
     *
     * @return Response The HTTP response object containing the rendered home page.
     */
    public function index(): Response
    {
        return $this->render('home/index', [
            'title' => 'Simple PHP Framework',
        ]);
    }

    /**
     * Handles the license page rendering.
     *
     * @return Response The HTTP response object with the rendered license page.
     */
    public function license(): Response
    {
        return $this->render('home/license', [
            'title' => 'MIT License, Copyright (c) 2025',
        ]);
    }


    /**
     * @param HomePageService $service
     * @return Response
     */
    public function welcome(HomePageService $service): Response
    {
        return $this->render('home/welcome', array_merge($this->params, [
            'service' => $service,
            'title' => 'Dynamic page with params in url',
        ]));
    }

    /**
     * Handles the contact page rendering.
     *
     * @return Response The HTTP response object with the rendered contact page.
     */
    public function contact(): Response
    {
        return $this->render('home/contact', [
            'title' => 'Contact page',
        ]);
    }


}