<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Todo\Entities\Bord;
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
        $boardId = $request->input('board_id');

    if (!$boardId) {
        return $this->respondInternalError('board_id در بدنه درخواست وجود ندارد');
    }

    $board = Bord::find($boardId);

    if (!$board) {
        return $this->respondNotFound('بورد مورد نظر یافت نشد');
    }

    $userId = auth()->user()->id;

    $board->likes()->toggle([$userId]);

    $likesCount = $board->likes()->count();

    return $this->respondSuccess('لایک بورد با موفقیت تغییر کرد', ['likes_count' => $likesCount]);    
    // } catch (\Exception $e) {
    //     return $this->respondInternalError('خطایی در تغییر وضعیت لایک رخ داده است');
    // }
    }
}
