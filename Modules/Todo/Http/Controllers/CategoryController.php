<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Todo\Entities\Bord;
use Modules\Todo\Entities\Category;
use Modules\Todo\Entities\Workspace;
use Modules\Todo\Http\Controllers\Contract\ApiController;
use Modules\Todo\Http\Requests\Category\CreateCategoryRequset;
use Modules\Todo\Http\Requests\Category\UpdateCategotyRequset;

class CategoryController extends ApiController
{
   /**
     * Display a listing of the resource.
     */
public function index(Request $request, $board_id)
{
    try {
        $board = Bord::find($board_id);
        if (!$board) {
            return response()->json(['error' => 'بورد یافت نشد'], 404);
        }

        $workspaceId = $board->workspace_id;

        $paginate = $request->input('paginate') ?? 10;
        $sortColumn = $request->input('sort', 'id');
        $sortDirection = \Illuminate\Support\Str::startsWith($sortColumn, '-') ? 'desc' : 'asc';
        $sortColumn = ltrim($sortColumn, '-');

        $categories = Category::with('tasks')
                                ->where('bord_id', $board_id)
                                ->orderBy($sortColumn, $sortDirection)
                                ->simplePaginate($paginate);

        return $this->respondSuccess('لیست دسته‌بندی‌ها با موفقیت دریافت شد', $categories);
    } catch (\Exception $e) {
        return $this->respondInternalError('خطایی در دریافت لیست دسته‌بندی‌ها رخ داده است');
    }
}
/**
 * Store a newly created resource in storage.
 */
public function store(CreateCategoryRequset $request)
{
    try {
        $boardId = $request->input('board_id');

        if (!$boardId) {
            return response()->json(['error' => 'شناسه Board در بدنه درخواست مشخص نشده است'], 400);
        }

        $board = Bord::where('id', $boardId)
                        ->whereHas('workspace', function ($query) {
                            $query->where('user_id', auth()->id());
                        })
                        ->firstOrFail();

        $category = Category::create([
            'name' => $request->input('name'),
            'bord_id' => $board->id,
        ]);

        return $this->respondCreated('دسته‌بندی با موفقیت ساخته شد', ['name' => $category->name]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return $this->respondNotFound('Board معتبر پیدا نشد یا به کاربر تعلق ندارد');
    } catch (\Exception $e) {
        return $this->respondInternalError('خطایی در ایجاد دسته‌بندی رخ داده است');
    }
}

/**
 * Show the specified resource.
 */
public function show($id, $board_id)
{
    try {
        $category = Category::where('id', $id)
                            ->where('bord_id', $board_id)
                            ->firstOrFail();

        return $this->respondSuccess('دسته‌بندی با موفقیت پیدا شد', $category);
    } catch (ModelNotFoundException $e) {
        return $this->respondNotFound('دسته‌بندی مورد نظر یافت نشد یا به Board مربوطه تعلق ندارد');
    } catch (\Exception $e) {
        return $this->respondInternalError('خطایی در نمایش اطلاعات دسته‌بندی رخ داده است');
    }
}
/**
 * Update the specified resource in storage.
 */
public function update(UpdateCategotyRequset $request, $id)
{
    try {
        $boardId = $request->input('board_id');

        if (!$boardId) {
            return response()->json(['error' => 'شناسه Board در بدنه درخواست مشخص نشده است'], 400);
        }

        $category = Category::where('id', $id)
                            ->where('bord_id', $boardId)
                            ->firstOrFail();

        $validated = $request->validated();
        $category->update($validated);

        return $this->respondSuccess('دسته‌بندی با موفقیت به‌روزرسانی شد', ['name' => $category->name]);
    } catch (ModelNotFoundException $e) {
        return $this->respondNotFound('دسته‌بندی مورد نظر یافت نشد یا به Board مربوطه تعلق ندارد');
    } catch (\Exception $e) {
        return $this->respondInternalError('خطایی در به‌روزرسانی دسته‌بندی رخ داده است');
    }
}
/**
 * Remove the specified resource from storage.
 */
public function destroy($id, $board_id)
{
    try {
        $category = Category::where('id', $id)
                            ->where('bord_id', $board_id)
                            ->firstOrFail();

        $category->delete();

        return $this->respondSuccess('دسته‌بندی با موفقیت حذف شد', null);
    } catch (ModelNotFoundException $e) {
        return $this->respondNotFound('دسته‌بندی مورد نظر یافت نشد یا به Board مربوطه تعلق ندارد');
    } catch (\Exception $e) {
        return $this->respondInternalError('خطایی در حذف دسته‌بندی رخ داده است');
    }
}

}
