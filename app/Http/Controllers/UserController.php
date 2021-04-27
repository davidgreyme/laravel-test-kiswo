<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Http\Requests\UserRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display a listing of the users
     * @param User $model
     * @return Factory|View
     * @throws AuthorizationException
     */
    public function index(User $model)
    {
        return view('user.index', ['user' => $model->all()]);
    }

    /**
     * Show the form for creating a new user
     * @return Factory|View
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created user in storage
     * @param UserRequest $request
     * @param User $model
     * @return
     */
    public function store(UserRequest $request, User $model)
    {
        $model->create($request);
        return redirect()->route('user.index')->withStatus(__('User successfully created.'));
    }

    /**
     * Show the form for editing the specified user
     * @param User $user
     * @return Factory|View
     */
    public function edit(User $user)
    {
        return view('user.edit', ['user' => $user]);
    }

    /**
     * Update the specified user in storage
     * @param UserRequest $request
     * @param User $user
     * @return
     */
    public function update(UserRequest $request, User $user)
    {
        if ($user != auth()->user()) {
            abort(403);
        }

        $hasPassword = $request->get('password');
        $user->update(
            $request->merge([
                'password' => Hash::make($request->get('password'))
            ])->except([$hasPassword ? '' : 'password'])
        );

        return redirect()->route('user.index');
    }

    /**
     * Remove the specified user from storage
     * @param User $user
     * @return
     */
    public function destroy(User $user)
    {
        if ($user != auth()->user()) {
            abort(403);
        }
        $user->delete();
        return redirect()->route('user.index')->withStatus(__('User successfully deleted.'));
    }

    /**
     * return 5 users with the largest product creation count
     */
    public function get_users_with_max_products()
    {
        return Product::groupBy('user_id')
            ->selectRaw('user_id, count(*) as product_count')
            ->orderByDesc('product_count')
            ->limit(5)
            ->get();
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function user_list(Request $request)
    {
        $items = $this->get_users_with_max_products();
        $item_ary = array();
        foreach ($items as $item) {
            $user = User::find($item->user_id);
            if (!empty($user))
                array_push($item_ary, array(
                    'product_count' => $item->product_count,
                    'user' => $user
                ));
        }
        return view('user.list', ['items' => $item_ary]);
    }
}
