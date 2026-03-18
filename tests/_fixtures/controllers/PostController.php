<?php

declare(strict_types = 1);

namespace Tests\_fixtures\controllers;

use Amondar\Postman\Attributes\PostmanFormData;
use Tests\_fixtures\controllers\requests\ListPostRequest;
use Tests\_fixtures\DescriptionFactory;

/**
 * Class PostController
 *
 * @author Amondar-SO
 */
class PostController extends Controller
{
    public function index(ListPostRequest $request)
    {
        return [];
    }

    #[PostmanFormData([
        'my' => 'method data',
    ])]
    public function show()
    {
        return [];
    }

    #[PostmanFormData(DescriptionFactory::class)]
    public function store()
    {
        return [];
    }

    public function update()
    {
        return [];
    }

    public function delete()
    {
        return [];
    }
}
