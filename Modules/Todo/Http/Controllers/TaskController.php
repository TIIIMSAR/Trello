<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Modules\Todo\Entities\Category;
use Modules\Todo\Entities\Task;
use Modules\Todo\Http\Controllers\Contract\ApiController;
use Modules\Todo\Http\Requests\Task\CreateTaskRequst;
use Modules\Todo\Http\Requests\Task\UpdateTaskRequst;

class TaskController extends ApiController
{
    /**
 * Display a listing of the resource.
 */
public function index($categoryId, Request $request)
{
    try {
        $category = Category::find($categoryId);

        if (!$category) {
            return response()->json(['error' => ' دسته‌بندی مورد نظر یافت نشد لطفا ادرس را درست وادر کنید'], 404);
        }

        $paginate = $request->input('paginate') ?? 10;
        $sortColumn = $request->input('sort', 'id');
        $sortDirection = \Illuminate\Support\Str::startsWith($sortColumn, '-') ? 'desc' : 'asc';
        $sortColumn = ltrim($sortColumn, '-');

        $tasks = Task::where('category_id', $categoryId)
                        ->orderBy($sortColumn, $sortDirection)
                        ->simplePaginate($paginate);

        return $this->respondSuccess('لیست تسک‌ها با موفقیت دریافت شد', $tasks);
    } catch (\Exception $e) {
        return $this->respondInternalError('خطایی در دریافت لیست تسک‌ها رخ داده است');
    }
}
/**
 * Store a newly created resource in storage.
 */
public function store(CreateTaskRequst $request)
{
    try {
        $categoryId = $request->input('category_id');

        if (!$categoryId) {
            return response()->json(['error' => 'شناسه دسته‌بندی در بدنه درخواست مشخص نشده است'], 400);
        }

        $category = Category::find($categoryId);

        if (!$category) {
            return response()->json(['error' => 'دسته‌بندی مورد نظر یافت نشد'], 404);
        }

        $validated = $request->validated();
        $validated['category_id'] = $categoryId;

        $task = Task::create($validated);

        return $this->respondCreated('تسک با موفقیت ایجاد شد', $task);
    } catch (\Exception $e) {
        return $this->respondInternalError('خطایی در ایجاد تسک رخ داده است');
    }
}
/**
 * Show the specified resource.
 */
public function show($id, $categoryId)
{
    try {
        $category = Category::find($categoryId);

        if (!$category) {
            return response()->json(['error' => 'دسته‌بندی مورد نظر یافت نشد'], 404);
        }

        $task = Task::where('id', $id)
                    ->where('category_id', $categoryId)
                    ->firstOrFail();

        return $this->respondSuccess('تسک با موفقیت پیدا شد', $task);
    } catch (ModelNotFoundException $e) {
        return $this->respondNotFound('تسک مورد نظر یافت نشد یا به دسته‌بندی مورد نظر تعلق ندارد');
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
        $categoryId = $request->input('category_id');

        if (!$categoryId) {
            return response()->json(['error' => 'شناسه دسته‌بندی در بدنه درخواست مشخص نشده است'], 400);
        }

        $category = Category::find($categoryId);

        if (!$category) {
            return response()->json(['error' => 'دسته‌بندی مورد نظر یافت نشد'], 404);
        }

        $task = Task::where('id', $id)
                    ->where('category_id', $categoryId)
                    ->firstOrFail();

        $validated = $request->validated();

        $task->update($validated);

        return $this->respondSuccess('تسک با موفقیت به‌روزرسانی شد', $task);
    } catch (ModelNotFoundException $e) {
        return $this->respondNotFound('تسک مورد نظر برای به‌روزرسانی یافت نشد یا به دسته‌بندی مورد نظر تعلق ندارد');
    } catch (\Exception $e) {
        return $this->respondInternalError('خطایی در به‌روزرسانی تسک رخ داده است');
    }    
}
/**
 * Remove the specified resource from storage.
 */
public function destroy($id, $categoryId)
{
    try {
        $category = Category::find($categoryId);

        if (!$category) {
            return response()->json(['error' => 'دسته‌بندی مورد نظر یافت نشد'], 404);
        }

        $task = Task::where('id', $id)
                    ->where('category_id', $categoryId)
                    ->first();

        if (!$task) {
            return response()->json(['error' => 'تسک مورد نظر یافت نشد یا به دسته‌بندی مشخص شده تعلق ندارد'], 404);
        }

        $task->delete();

        return $this->respondSuccess('تسک با موفقیت حذف شد', null);
    } catch (\Exception $e) {
        return $this->respondInternalError('خطایی در حذف تسک رخ داده است');
    }
}
}
