<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Todo\Entities\Task;
use Modules\Todo\Http\Controllers\Contract\ApiController;
use Modules\Todo\Http\Requests\Task\CreateTaskRequst;
use Modules\Todo\Http\Requests\Task\UpdateTaskRequst;

class TaskController extends ApiController
{
      /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // try {
            $paginate = $request->input('paginate') ?? 10;
            $sortColumn = $request->input('sort', 'id');
            $sortDirection = Str::startsWith($sortColumn, '-') ? 'desc' : 'asc';
            $sortColumn = ltrim($sortColumn, '-');

            $tasks = Task::orderBy($sortColumn, $sortDirection)->simplePaginate($paginate);
            {
                // return $this->belongsTo(Task::class);
            }
            return $this->respondSuccess('لیست تسک‌ها با موفقیت دریافت شد', $tasks);
        // } catch (\Exception $e) {
        //     return $this->respondInternalError('خطایی در دریافت لیست تسک‌ها رخ داده است');
        // }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTaskRequst $request)
    {
        // try {
            $validated = $request->validated();
            $task = Task::create($validated);

            return $this->respondCreated('تسک با موفقیت ایجاد شد', $task);
        // } catch (\Exception $e) {
        //     return $this->respondInternalError('خطایی در ایجاد تسک رخ داده است');
        // }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $task = Task::findOrFail($id);
            return $this->respondSuccess('تسک با موفقیت پیدا شد', $task);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('تسک مورد نظر یافت نشد');
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در نمایش اطلاعات تسک رخ داده است');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequst $request, $id)
    {
        try {
            $validated = $request->validated();
            $task = Task::findOrFail($id);
            $task->update($validated);

            return $this->respondSuccess('تسک با موفقیت به‌روزرسانی شد', $task);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('تسک مورد نظر برای به‌روزرسانی یافت نشد');
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در به‌روزرسانی تسک رخ داده است');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();

            return $this->respondSuccess('تسک با موفقیت حذف شد', null);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('تسک مورد نظر برای حذف یافت نشد');
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در حذف تسک رخ داده است');
        }
    }

    public function fallback()
    {
        return $this->respondNotFound('لطفا ادرس را درست وارد بفرمایید ');
    }
}
