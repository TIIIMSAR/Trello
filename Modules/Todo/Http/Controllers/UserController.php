<?php

namespace Modules\Todo\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Todo\Entities\User;
use Modules\Todo\Http\Controllers\Contract\ApiController;
use Modules\Todo\Http\Requests\User\CreateUserRequset;
use Modules\Todo\Http\Requests\User\UpdateUserRequset;

class UserController extends ApiController
{
     /**    
         * Display a listing of the resource.
         */
        public function index(Request $request)
        {
            try {
                $paginate = $request->input('paginate') ?? 10;
                $sortColumn = $request->input('sort', 'id');
                $sortDirection = Str::startsWith($sortColumn, '-') ? 'desc' : 'asc';
                $sortColumn = ltrim($sortColumn, '-');

                $users = User::orderBy($sortColumn, $sortDirection)->simplePaginate($paginate);
                return $this->respondSuccess('لیست کاربران با موفقیت دریافت شد', $users);
            } catch (\Exception $e) {
                return $this->respondInternalError('خطایی در دریافت لیست کاربران رخ داده است');
            }
        }

        /**
         * Show the specified resource.
         */
        public function show($id)
        {
            try {
                $user = User::findOrFail($id);
                return $this->respondSuccess('کاربر با موفقیت پیدا شد', $user);
            } catch (ModelNotFoundException $e) {
                return $this->respondNotFound('کاربر مورد نظر یافت نشد');
            } catch (\Exception $e) {
                return $this->respondInternalError('خطایی در نمایش اطلاعات کاربر رخ داده است');
            }
        }

        /**
         * Update the specified resource in storage.
         */
        public function update(UpdateUserRequset $request, $id)
        {
            // $file =  $request->file('image_path');
            //     if(!empty($file)){
            //         $image_naem = time() . rand(100,10000) . '.' . $file->getClientOriginalExtension();
            //         $file->move('images/UserProfile', $image_naem);
            //         $validated['image_path'] = $image_naem;
            // }
            
            try {
                $validated = $request->validated();
                

                $user = User::findOrFail($id);
                $user->update($validated);

                return $this->respondSuccess('کاربر با موفقیت به‌روزرسانی شد', $user);
            } catch (ModelNotFoundException $e) {
                return $this->respondNotFound('کاربر مورد نظر برای به‌روزرسانی یافت نشد');
            } catch (\Exception $e) {
                return $this->respondInternalError('خطایی در به‌روزرسانی کاربر رخ داده است');
            }
        }

        /**
         * Remove the specified resource from storage.
         */
        public function destroy($id)
        {
            try {
                $user = User::findOrFail($id);
                $user->delete();

                return $this->respondSuccess('کاربر با موفقیت حذف شد', null);
            } catch (ModelNotFoundException $e) {
                return $this->respondNotFound('کاربر مورد نظر برای حذف یافت نشد');
            } catch (\Exception $e) {
                return $this->respondInternalError('خطایی در حذف کاربر رخ داده است');
            }
        }
}
