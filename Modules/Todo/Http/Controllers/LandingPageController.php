<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Todo\Entities\Workspace;
use Modules\Todo\Transformers\CategoryResource;

class LandingPageController extends Controller
{
    public function index($workspace_id)
    {
        $workspace = Workspace::with('categories.tasks')->findOrFail($workspace_id);

        return CategoryResource::collection($workspace->categories);
    }
}
