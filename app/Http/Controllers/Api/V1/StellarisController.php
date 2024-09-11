<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Services\Stellaris\StellarisTraitService;

class StellarisController extends ApiController
{
    protected $stellarisDataService;

    public function __construct(StellarisTraitService $stellarisDataService)
    {
        $this->stellarisDataService = $stellarisDataService;
    }

    public function updateGameData()
    {
        $filePath = storage_path('app/stellaris/common/traits/00_scientist_traits.txt');
        $fileContent = $this->stellarisDataService->readGameFile($filePath);
        $parsedData = $this->stellarisDataService->parseGameData($fileContent);
        return $this->successResponse($parsedData, 'Data scraped and parsed');
        $this->stellarisDataService->saveToDatabase($parsedData);

        return response()->json(['message' => 'Game data updated successfully.']);
    }
}
