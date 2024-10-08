<?php

namespace App\Http\Controllers\Api\v1;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Swagger with Laravel Sample API Project",
 *      description="L5 Swagger OpenApi description",
 *      @OA\Contact(
 *          email="admin@admin.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Demo API Server"
 * )
 *
 * @OA\Tag(
 *     name="Projects",
 *     description="API Endpoints of Projects"
 * )
 */
abstract class Controller extends \App\Http\Controllers\Controller
{
    //
}
