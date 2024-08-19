<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Todo\Entities\Workspace;
use Modules\Todo\Http\Controllers\Contract\ApiController;
use Modules\Todo\Http\Requests\Workspace\CreateWorkspaceRequst;
use Modules\Todo\Http\Requests\Workspace\UpdateWorkspaceRequset;

class WorkspaceController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    try {
        $userId = auth()->id();

        $paginate = $request->input('paginate') ?? 10;
        $sortColumn = $request->input('sort', 'id');
        $sortDirection = \Illuminate\Support\Str::startsWith($sortColumn, '-') ? 'desc' : 'asc';
        $sortColumn = ltrim($sortColumn, '-');

        $workspaces = Workspace::where('user_id', $userId)
            ->orderBy($sortColumn, $sortDirection)
            ->simplePaginate($paginate);

        return $this->respondSuccess('لیست پوشه‌ها با موفقیت دریافت شد', $workspaces);
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
            $workspaces = Workspace::create([
                'name' => $validated['name'],
                'user_id' => auth()->user()->id
            ]);

            return $this->respondCreated('پوشه با موفقیت ساخته شد', ['name' => $workspaces['name']]);
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
            $workspace = Workspace::findOrFail($id);
    
            if ($workspace->user_id !== auth()->id()) {
                return $this->respondForbidden('شما مجاز به مشاهده این پوشه نیستید.');
            }
    
            return $this->respondSuccess('پوشه با موفقیت پیدا شد', $workspace);
    
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
    
            $workspace = Workspace::findOrFail($id);
    
            if ($workspace->user_id !== auth()->id()) {
                return $this->respondForbidden('شما مجاز به ویرایش این پوشه نیستید.');
            }
    
            $workspace->update($validated);
    
            return $this->respondSuccess('پوشه با موفقیت به‌روزرسانی شد', ['name' => $workspace->name]);
    
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
            $workspace = Workspace::findOrFail($id);
    
            if ($workspace->user_id !== auth()->id()) {
                return $this->respondForbidden('شما مجاز به ویرایش این پوشه نیستید.');
            }
            
           $workspace->delete();

            return $this->respondSuccess('پوشه با موفقیت پاک شد', null);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('پوشه مورد نظر برای حذف یافت نشد');
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در حذف پوشه رخ داده است');
        }
    }
}
