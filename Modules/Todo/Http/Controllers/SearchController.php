<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Todo\Entities\Task;
use Modules\Todo\Http\Controllers\Contract\ApiController;

class SearchController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Task::where('title', 'LIKE', "%{$request->search}%");
            $results = $query->simplePaginate(10);

            $results->appends(request()->query());
            return $this->respondSuccess('نتایج جستجو با موفقیت دریافت شد', $results);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('هیچ تسکی مطابق با جستجوی شما یافت نشد');
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در جستجوی تسک‌ها رخ داده است');
        }
    }
}
