<?php

// app/Controllers/TodoController.php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TodoModel;

class Todo extends BaseController
{
    public function __construct()
    {
        $this->TodoModel = new TodoModel;
    }
    public function index()
    {

        $data['todos'] =  $this->TodoModel->findAll();

        return view('admin/todos/todo', $data);
    }

    public function add()
    {
        $this->TodoModel = new TodoModel();

        $data = [
            'text' => $this->request->getPost('text'),
            'is_completed' => false,
            'priority' => $this->request->getPost('priority'),
            'due_time' => $this->request->getPost('due_time')
        ];

        if ($this->TodoModel->save($data)) {
            // Return the ID of the newly created task
            $data['id'] =  $this->TodoModel->insertID();
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Task added successfully',
                'data' => $data
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to add task'
        ]);
    }


    public function edit($id)
    {
        $this->TodoModel = new TodoModel();
        $todo =  $this->TodoModel->find($id);

        if ($todo) {
            $data = [
                'id' => $id,
                'text' => $this->request->getPost('text'),
                'priority' => $this->request->getPost('priority'),
                'due_time' => $this->request->getPost('due_time'),
            ];

            if ($this->TodoModel->save($data)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Task updated successfully',
                    'data' => $data
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to update task'
        ]);
    }

    public function delete($id)
    {
        $this->TodoModel = new TodoModel();

        if ($this->TodoModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Task deleted successfully',
                'id' => $id
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to delete task'
        ]);
    }

    public function toggleComplete($id)
    {
        $this->TodoModel = new TodoModel();
        $todo =  $this->TodoModel->find($id);

        if ($todo) {
            $todo['is_completed'] = !$todo['is_completed'];
            if ($this->TodoModel->save($todo)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'is_completed' => true,
                    'message' => 'Task status updated',
                    'data' => $todo
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to update task status'
        ]);
    }
}
