<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Todo\Entities\Bord;
use Modules\Todo\Entities\Workspace;
use Modules\Todo\Http\Controllers\Contract\ApiController;
use Modules\Todo\Transformers\CategoryResource;

class LandingPageController extends ApiController
{
    public function index($workspace_id)
    {
    try {
        $workspace = Bord::with('categories.tasks')->findOrFail($workspace_id);

        return response()->json([
            'success' => true,
            'message' => 'دسته‌بندی‌ها و وظایف با موفقیت بازیابی شدند.',
            'data' => CategoryResource::collection($workspace->categories),
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'خطا در بازیابی دسته‌بندی‌ها و وظایف: ' . $e->getMessage(),
        ], 500);
    }   
   
    }  
}
