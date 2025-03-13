<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\AppService;

class UserController extends Controller
{
    public function get($id)
    {
        $user = User::whereId($id)->first(['id', 'name', 'cpf', 'cep', 'address', 'birth_date', 'email']);

        if (!$this->validateUser($user)) {
            return response()->json([
                'success' => false,
                'message' => 'O usuário não foi encontrado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
        ], 200);
    }

    public function create(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|max:255|confirmed',
            'birth_date' => 'required|date',
        ];
    
        if ($validation = AppService::validateRequest($request->all(), $rules)) {
            return response()->json($validation, $validation['status_code']);
        }

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Usuário registrado com sucesso!',
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'password' => 'nullable|min:6|max:255|confirmed',
            'birth_date' => 'required|date',
            'cpf' => 'required|digits:11',
            'cep' => 'required|digits:8',
            'address' => 'required|max:255',
        ];
    
        if ($validation = AppService::validateRequest($request->all(), $rules)) {
            return response()->json($validation, $validation['status_code']);
        }

        $user = User::whereId($id)->first();
        
        if (!$this->validateUser($user)) {
            return response()->json([
                'success' => false,
                'message' => 'O usuário não foi encontrado.',
            ], 404);
        }

        $data = $request->all();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso!',
        ], 200);
    }

    public function delete($id)
    {
        $user = User::whereId($id)->first();

        if (!$this->validateUser($user)) {
            return response()->json([
                'success' => false,
                'message' => 'O usuário não foi encontrado.',
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'O usuário foi removido com sucesso!',
        ], 200);
    }

    private function validateUser($user): bool
    {
        if (!$user || $user->id !== Auth::id()) {
            return false;
        }
    
        return true;
    }
}
