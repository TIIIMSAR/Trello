<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Todo\Entities\Bord;
use Modules\Todo\Entities\Workspace;
use Modules\Todo\Http\Controllers\Contract\ApiController;
use Modules\Todo\Http\Requests\Bord\CreateBordRequest;
use Modules\Todo\Http\Requests\Bord\DeleteBordRequest;
use Modules\Todo\Http\Requests\Bord\UpdateBordRequest;
use Modules\Todo\Http\Requests\Workspace\CreateWorkspaceRequst;
use Modules\Todo\Http\Requests\Workspace\UpdateWorkspaceRequset;

class BordController extends ApiController
{

      public function index(Request $request, $workspaceId)
{ 
    try {
        if (!$workspaceId) {
            return $this->respondInternalError('شناسه میزکار وارد نشده است');
        }

        $user = $request->user();

        $workspace = $user->workspaces()->where('id', $workspaceId)->first();

        if (!$workspace) {
            $workspaceExists = Workspace::find($workspaceId);
            if (!$workspaceExists) {
                return $this->respondNotFound('تخته شما یافت نشد مطمعن شوید که ان وجود دارد!');
            }
            return $this->respondInternalError('شما مجاز به دسترسی به این تخته نیستید');
        }

        $paginate = $request->input('paginate') ?? 10;
        $sortColumn = $request->input('sort', 'id');
        $sortDirection = \Illuminate\Support\Str::startsWith($sortColumn, '-') ? 'desc' : 'asc';
        $sortColumn = ltrim($sortColumn, '-');

        $bord = Bord::where('workspace_id', $workspaceId)
            ->orderBy($sortColumn, $sortDirection)
            ->simplePaginate($paginate);

        return $this->respondSuccess('لیست تخته‌ها با موفقیت دریافت شد', $bord);
    } catch (ModelNotFoundException $e) {
        return $this->respondNotFound('تخته مورد نظر پیدا نشد');
    } catch (\Illuminate\Database\QueryException $e) {
        return $this->respondInternalError('خطایی در پایگاه داده رخ داده است');
    } catch (\Exception $e) {
        return $this->respondInternalError('یک خطای ناشناخته رخ داده است');
    }
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateBordRequest $request)
    {
        try {
            $validated = $request->validated();

            $bord = Bord::create([
                'name' => $validated['name'],
                'workspace_id' => $validated['workspace_id'],
            ]);
        
            return $this->respondCreated('تخته با موفقیت ساخته شد', ['name' => $bord['name']]);
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در ایجاد تخته رخ داده است');
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBordRequest $request, $id)
    {
        try {
            $validated = $request->validated();
    
            $bord = Bord::findOrFail($id);
    
            $workspaceId = $validated['workspace_id'] ?? null;
            if (!$workspaceId) {
                return $this->respondInternalError('شناسه میزکار وارد نشده است.');
            }
    
            $workspace = Workspace::where('id', $workspaceId)
                                  ->where('user_id', auth()->id())
                                  ->first();
    
            if (!$workspace) {
                return $this->respondInternalError('شما مجاز به ویرایش تخته‌های این میزکار نیستید.');
            }
    
            if ($bord->workspace_id != $workspaceId) { 
                return $this->respondInternalError('این تخته به میزکار مشخص‌ شده تعلق ندارد.');
            }
    
            $bord->update($validated);
    
            return $this->respondSuccess('تخته با موفقیت به‌روزرسانی شد', ['name' => $bord->name]);
    
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('تخته مورد نظر برای به‌روزرسانی یافت نشد');
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->respondInternalError('خطایی در پایگاه داده رخ داده است');
        } catch (\Exception $e) {
            return $this->respondInternalError('یک خطای ناشناخته رخ داده است');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $workspaceId)
    {   
        try {
            if (!$workspaceId) {
                return $this->respondInternalError('شناسه میزکار وارد نشده است');
            }

            $bord = Bord::findOrFail($id);
    
            if ($bord->workspace_id != $workspaceId) {
                return $this->respondInternalError('این تخته به میزکار مشخص‌ شده تعلق ندارد.');
            }
    
            $workspace = Workspace::where('id', $workspaceId)
                ->where('user_id', auth()->id())
                ->first();
    
            if (!$workspace) {
                return $this->respondInternalError('شما مجاز به حذف تخته‌های این میزکار نیستید.');
            }
    
            $bord->delete();
    
            return $this->respondSuccess('تخته با موفقیت پاک شد', null);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound(' تخته مورد نظر برای حذف یافت نشد برسی کنید اطلاعات درست هستند');
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->respondInternalError('خطایی در پایگاه داده رخ داده است');
        } catch (\Exception $e) {
            return $this->respondInternalError('یک خطای ناشناخته رخ داده است');
        }
    }
    
}

