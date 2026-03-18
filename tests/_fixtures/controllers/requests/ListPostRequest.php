<?php

declare(strict_types = 1);

namespace Tests\_fixtures\controllers\requests;

use Amondar\Postman\Attributes\PostmanFormData;

/**
 * Class ListPostRequest
 *
 * @author Amondar-SO
 */
#[PostmanFormData(['my' => 'request data'])]
class ListPostRequest
{
    //
}
