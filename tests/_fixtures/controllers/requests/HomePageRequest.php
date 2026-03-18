<?php

declare(strict_types = 1);

namespace Tests\_fixtures\controllers\requests;

use Amondar\Postman\Attributes\PostmanFormData;

/**
 * Class HomePageRequest
 *
 * @author Amondar-SO
 */
#[PostmanFormData(['my' => 'home page request data'])]
class HomePageRequest {}
