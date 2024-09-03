<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\LeaderTrait;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Api\ApiController;

class LeaderTraitController extends ApiController
{
    public function index(): JsonResponse
    {
        $traits = LeaderTrait::all();

        return $this->successResponse($traits, 'Traits fetched correctly');
    }

    public function show(int $id): JsonResponse
    {
        $trait = LeaderTrait::find($id);

        return $this->successResponse($trait, 'Trait ' . $trait->name . ' fetched correctly');
    }
}