<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Todo\Entities\Task;
use Modules\Todo\Entities\Workspace;
use Modules\Todo\Http\Controllers\Contract\ApiController;

class SearchController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $workspaceId = $request->header('workspace_id');
    
            $workspace = Workspace::find($workspaceId);
    
            if (!$workspace) {
                return response()->json(['error' => 'فضای کاری مورد نظر یافت نشد'], 404);
            }
    
            $categoryIds = $workspace->categories()->pluck('id');
            
            $searchTerm = $request->query('search', '');
    
            $tasks = Task::whereIn('category_id', $categoryIds)
                ->where('title', 'LIKE', "%{$searchTerm}%")
                ->simplePaginate(10);
    
            $tasks->appends(request()->query());
    
            return $this->respondSuccess('نتایج جستجو با موفقیت دریافت شد', $tasks);
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در جستجوی تسک‌ها رخ داده است');
        }
    }
}
