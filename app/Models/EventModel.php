<?php

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'start', 'end', 'className'];

    public function getAllEvents()
    {
        return $this->findAll();
    }

    public function addEvent($data)
    {
        return $this->insert($data);
    }

    public function updateEvent($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteEvent($id)
    {
        return $this->delete($id);
    }
}
