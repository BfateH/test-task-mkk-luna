<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Organizations, Buildings, Activities API",
 *     version="1.0.0",
 *     description="API для управления организациями, зданиями и видами деятельности",
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Основной API сервер"
 * )
 *
 * @OA\Components(
 *      @OA\SecurityScheme(
 *          securityScheme="apiKeyAuth",
 *          type="apiKey",
 *          in="header",
 *          name="X-API-KEY",
 *          description="API Key для аутентификации"
 *      )
 *  )
 *
 * @OA\Tag(
 *     name="Organizations",
 *     description="Операции с организациями"
 * )
 *
 * @OA\Tag(
 *     name="Buildings",
 *     description="Операции со зданиями"
 * )
 *
 * @OA\Tag(
 *     name="Activities",
 *     description="Операции с видами деятельности"
 * )
 *
 * @OA\Schema(
 *     schema="Error",
 *     type="object",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         @OA\Property(property="error", type="string", example="Какая-то ошибка")
 *
 *     ),
 * )
 *
 * @OA\OpenApi(
 *     security={{"apiKeyAuth": {}}}
 * )
 */
abstract class Controller
{
    //
}
