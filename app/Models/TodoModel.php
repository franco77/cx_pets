<?php
// app/Models/TodoModel.php

namespace App\Models;

use CodeIgniter\Model;

class TodoModel extends Model
{
    protected $table      = 'todos';
    protected $primaryKey = 'id';

    protected $allowedFields = ['text', 'is_completed', 'priority', 'due_time'];

    // Additional configuration
    protected $useTimestamps = true; // Assuming you have created_at and updated_at fields
}
