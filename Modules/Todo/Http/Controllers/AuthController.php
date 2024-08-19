<?php

namespace Modules\Todo\Http\Controllers;

use CreateUsersTable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;    
use Illuminate\Support\Facades\Validator;
use Modules\Todo\Entities\User;
use Modules\Todo\Http\Controllers\Contract\ApiController;
use Modules\Todo\Http\Requests\User\CreateUserRequset;
use Modules\Todo\Http\Requests\User\UpdateUserRequset;

class AuthController extends ApiController
{
      public function register(CreateUserRequset $request)
      {
         $file =  $request->file('image_path');
                    if(!empty($file)){
                        $image_naem = time() . rand(100,10000) . '.' . $file->getClientOriginalExtension();
                        $file->move('images/UserProfile', $image_naem);
                        $validated['image'] = $image_naem;
                    }
            try{    
                $request['password'] = Hash::make($request->password);
                    
                $user = User::create([
                    'name' => $request['name'],
                    'email' => $request['email'],
                    'password' => $request['password'],
                ]);
                    
                $token = $user->createToken('Personal Access Token')->plainTextToken;
                return $this->respondCreated('کاربر با موفقیت ایجاد شد', [
                    'user' => $user,
                    'token' => $token,
                ]);
            } catch (\Exception $e) {
                return $this->respondInternalError('(ایمیل باید یونیک باشد):خطایی در ایجاد کاربر رخ داده است');
            }
      }
  


      public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'اطلاعات ورود اشتباه است'], 401);
    }

    $token = $user->createToken('Personal Access Token')->plainTextToken;

    return response()->json([
        'message' => 'ورود موفقیت‌آمیز',
        'user' => $user,
        'token' => $token,
    ]);
}

    public function logout($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->tokens()->delete();
            $user->delete();

            return $this->respondSuccess('کاربر با موفقیت حذف شد', null);
        } catch (ModelNotFoundException $e) {
            return $this->respondNotFound('کاربر مورد نظر برای حذف یافت نشد');
        } catch (\Exception $e) {
            return $this->respondInternalError('خطایی در حذف کاربر رخ داده است');
        }
    }


}
