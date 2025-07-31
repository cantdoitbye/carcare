<?php

namespace App\Repositories;

use App\Models\AdminUser;
use App\Repositories\Contracts\AdminUserRepositoryInterface;

class AdminUserRepository implements AdminUserRepositoryInterface
{
   public function __construct(
        protected AdminUser $model
    ) {}

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $admin = $this->find($id);
        $admin->update($data);
        return $admin;
    }

    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function updateLastLogin($id)
    {
        return $this->update($id, ['last_login_at' => now()]);
    }

    public function getActiveAdmins()
    {
        return $this->model->where('status', 'active')->get();
    }
}