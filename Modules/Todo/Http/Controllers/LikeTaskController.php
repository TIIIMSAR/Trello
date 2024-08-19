<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Todo\Entities\Category;
use Modules\Todo\Entities\Task;
use Modules\Todo\Entities\User;
use Modules\Todo\Entities\Workspace;
use Modules\Todo\Http\Controllers\Contract\ApiController;

class LikeTaskController extends ApiController
{
    /**
     * Toggle the like for a task.  
     */
    public function index(Request $request)
    {
        // try {
            $workspaceId = $request->header('workspace_id');
    
            if (!$workspaceId) {
                return $this->respondInternalError('workspace_id در هدر درخواست وجود ندارد');
            }
    
            $categories = Category::where('workspace_id', $workspaceId)->pluck('id');
    
            if ($categories->isEmpty()) {
                return $this->respondNotFound('هیچ دسته‌بندی‌ای برای workspace_id مشخص شده پیدا نشد');
            }

            $user = Workspace::whereIn('category_id', $categories)->get();
    
            if ($user->isEmpty()) {
                return $this->respondNotFound('هیچ تسکی برای دسته‌بندی‌های مشخص شده پیدا نشد');
            }
    
            $userId = auth()->user()->id;
            foreach ($user as $task) {
                $task->likes()->toggle([$userId]);
            }
    
            $likesCount = $user->sum(function ($task) {
                return $task->likes()->count();
            });
    
            return $this->respondSuccess('لایک‌ها با موفقیت تغییر کرد', ['likes_count' => $likesCount]);
        // } catch (\Exception $e) {
        //     return $this->respondInternalError('خطایی در تغییر وضعیت لایک رخ داده است');
        // }
        
    }
}
