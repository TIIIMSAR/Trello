<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Todo\Entities\Task;

class LikeTaskController extends Controller
{
    /**
     * Toggle the like for a task.
     */
    public function index(Task $task)
    {
        try {
            $task->likes()->toggle([auth()->user()->id]);

            return $this->respondSuccess('لایک با موفقیت تغییر کرد', ['likes_count' => $task->likes()->count()]);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('تسک مورد نظر یافت نشد');
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در تغییر وضعیت لایک رخ داده است');
        }
    }
}
