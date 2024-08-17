<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Todo\Entities\Workspace;
use Modules\Todo\Http\Requests\Folder\CreateWorkspaceRequst;
use Modules\Todo\Http\Requests\Folder\UpdateWorkspaceRequset;

class WorkspaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $paginate = $request->input('paginate') ?? 10;
            $sortColumn = $request->input('sort', 'id');
            $sortDirection = \Illuminate\Support\Str::startsWith($sortColumn, '-') ? 'desc' : 'asc';
            $sortColumn = ltrim($sortColumn, '-');

            $folders = Workspace::orderBy($sortColumn, $sortDirection)->simplePaginate($paginate);
            return $this->respondSuccess('لیست پوشه‌ها با موفقیت دریافت شد', $folders);
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در دریافت لیست پوشه‌ها رخ داده است');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateWorkspaceRequst $request)
    {
        try {
            $validated = $request->validated();
            $folder = Workspace::create($validated);

            return $this->respondCreated('پوشه با موفقیت ساخته شد', ['title' => $folder['title']]);
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در ایجاد پوشه رخ داده است');
        }
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        try {
            $folder = Workspace::findOrFail($id);
            return $this->respondSuccess('پوشه با موفقیت پیدا شد', $folder);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('پوشه مورد نظر یافت نشد');
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در نمایش اطلاعات پوشه رخ داده است');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkspaceRequset $request, $id)
    {
        try {
            $validated = $request->validated();
            $folder = Workspace::findOrFail($id);
            $folder->update($validated);

            return $this->respondSuccess('پوشه با موفقیت به‌روزرسانی شد', ['title' => $folder['title']]);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('پوشه مورد نظر برای به‌روزرسانی یافت نشد');
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در به‌روزرسانی پوشه رخ داده است');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $folder = Workspace::findOrFail($id);
            $folder->delete();

            return $this->respondSuccess('پوشه با موفقیت پاک شد', null);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('پوشه مورد نظر برای حذف یافت نشد');
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در حذف پوشه رخ داده است');
        }
    }
}
