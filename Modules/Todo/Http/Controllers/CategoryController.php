<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Todo\Entities\Category;
use Modules\Todo\Http\Requests\Category\CreateCategoryRequset;
use Modules\Todo\Http\Requests\Category\UpdateCategotyRequset;

class CategoryController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        // try {
            $paginate = $request->input('paginate') ?? 10;
            $sortColumn = $request->input('sort', 'id');
            $sortDirection = \Illuminate\Support\Str::startsWith($sortColumn, '-') ? 'desc' : 'asc';
            $sortColumn = ltrim($sortColumn, '-');

            $categories = Category::with('tasks')->orderBy($sortColumn, $sortDirection)->simplePaginate($paginate);
            return $this->respondSuccess('لیست دسته‌بندی‌ها با موفقیت دریافت شد', $categories);
        // } catch (\Exception $e) {
        //     return $this->respondInternalError('خطایی در دریافت لیست دسته‌بندی‌ها رخ داده است');
        // }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategoryRequset $request)
    {
        try {
            $validated = $request->validated();
            $category = Category::create($validated);

            return $this->respondCreated('دسته‌بندی با موفقیت ساخته شد', ['slug' => $category['slug']]);
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در ایجاد دسته‌بندی رخ داده است');
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            return $this->respondSuccess('دسته‌بندی با موفقیت پیدا شد', $category);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('دسته‌بندی مورد نظر یافت نشد');
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
            $validated = $request->validated();
            $category = Category::findOrFail($id);
            $category->update($validated);

            return $this->respondSuccess('دسته‌بندی با موفقیت به‌روزرسانی شد', ['slug' => $category['slug']]);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('دسته‌بندی مورد نظر برای به‌روزرسانی یافت نشد');
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در به‌روزرسانی دسته‌بندی رخ داده است');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            return $this->respondSuccess('دسته‌بندی با موفقیت حذف شد', null);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('دسته‌بندی مورد نظر برای حذف یافت نشد');
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در حذف دسته‌بندی رخ داده است');
        }
    }
}
