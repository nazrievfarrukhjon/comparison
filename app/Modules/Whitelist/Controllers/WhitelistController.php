<?php

namespace App\Modules\Whitelist\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Blacklist\BlacklistEntity;
use App\Modules\Blacklist\Requests\CompareToBlacklistRequest;
use App\Modules\Whitelist\WhitelistEntity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WhitelistController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/persons/blacklist/find",
     *     summary="найти из списка по ф.и.о.",
     *     tags={"Blacklist"},
     *     security={{"blacklists": {}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={
     *                  "api_token",
     *                  "search_key",
     *                  "date_of_birth",
     *                  "operation_type"
     *              },
     *              @OA\Property(property="api_token", type="string", example="123"),
     *              @OA\Property(property="initials", type="string", example="alcaldeli nares angel"),
     *              @OA\Property(property="date_of_birth", type="date", example=""),
     *              @OA\Property(property="operation_type", type="string", example="test"),
     *              @OA\Property(property="search_key", type="string", example="trgm")
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              @OA\Property(property="result", type="boolean", example="false"),
     *              @OA\Property(property="data_list", type="object", example=""),
     *              @OA\Property(property="max_sim", type="float", example="1"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Not Found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="")
     *          )
     *     ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=500,
     *          description="Server error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="cURL error 7: Failed to connect..."),
     *          )
     *     ),
     * )
     * =========================================*
     */
    public function find(CompareToBlacklistRequest $request): JsonResponse
    {
        $blacklistEntity = new WhitelistEntity($request->search_key);
        $blacklistEntity->find();
        $similarity = $blacklistEntity->similarity();

        return response()->json(['similarity' => $similarity], 200);
    }

    public function index(Request $request)
    {

    }

    public function delete(int $id)
    {

    }

    public function store(Request $request)
    {

    }

    public function update(Request $request, int $id)
    {

    }

}
