<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PetsModel;
use App\Models\SpeciesModel;
use App\Models\BreedsModel;
use App\Models\OwnersModel;

class Pets extends BaseController
{
   use ResponseTrait;

   public function __construct()
   {
      $this->PetsModel = new PetsModel;
      $this->SpeciesModel = new SpeciesModel;
      $this->BreedsModel = new BreedsModel;
      $this->OwnersModel = new OwnersModel;
   }

   function index()
   {
      $data = [
         'title' => 'Data Pets',
         'host' => site_url('pets/')
      ];
      echo view('admin/pets/list', $data);
   }

   public function data()
   {
      try {
         $request = esc($this->request->getPost());
         $search = $request['search']['value'];
         $limit = $request['length'];
         $start = $request['start'];

         $orderIndex = $request['order'][0]['column'];
         $orderFields = $request['columns'][$orderIndex]['data'];
         $orderDir = $request['order'][0]['dir'];

         $recordsTotal = $this->PetsModel->countTotal();
         $data = $this->PetsModel->filter($search, $limit, $start, $orderFields, $orderDir);
         $recordsFiltered = $this->PetsModel->countFilter($search);

         $callback = [
            'draw' => $request['draw'],
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
         ];

         return $this->respond($callback);
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   public function getBreedsBySpecie($specie_id)
   {

      $breeds = $this->BreedsModel->where('id_specie', $specie_id)->findAll();

      return $this->response->setJSON($breeds); // Devolver las razas en formato JSON
   }

   public function new()
   {
      $data = [
         'data_owners' => $this->OwnersModel->findAll(),
         'data_species' => $this->SpeciesModel->findAll(),
         'data_breeds' => $this->BreedsModel->findAll(),
      ];

      echo view('admin/pets/form', $data);
   }

   public function create()
   {
      // Obtener los campos del formulario
      $request = [
         'owner_id' => $this->request->getPost('owner_id'),
         'name' => $this->request->getPost('name'),
         'sex' => $this->request->getPost('sex'),
         'age' => $this->request->getPost('age'),
         'weight' => $this->request->getPost('weight'),
         'weight_unit' => $this->request->getPost('weight_unit'),
         'specie_id' => $this->request->getPost('specie_id'),
         'breed_id' => $this->request->getPost('breed_id'),
         'profile' => $this->request->getPost('profile'),
         'created_at' => date('Y-m-d H:i:s')
      ];

      // Verifica si se subió una imagen
      $img = $this->request->getFile('photo');

      // Validar los datos
      $this->rules();
      if ($this->validation->run($request) != TRUE) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $this->validation->getErrors()
         ], 400);
      } else {
         try {
            // Manejar la imagen subida
            if ($img && $img->isValid() && !$img->hasMoved()) {
               // Generar un nombre único para la imagen
               $newName = $img->getRandomName();

               // Mover la imagen a la carpeta temporalmente
               $img->move('uploads/pets/original', $newName);

               // Redimensionar la imagen a 300x300 píxeles

               $this->imageService->withFile('uploads/pets/original/' . $newName);
               $this->imageService->resize(300, 300, true, 'height');
               $this->imageService->save('uploads/pets/' . $newName); // Guardar la imagen redimensionada en la carpeta destino

               // Opcional: Borrar la imagen original si no la necesitas
               unlink('uploads/pets/original/' . $newName);

               // Añadir el nombre de la imagen a la petición para guardar en la base de datos
               $request['photo'] = $newName;
            }

            // Insertar los datos en la base de datos
            $insert = $this->PetsModel->insert($request);

            if ($insert) {
               return $this->respondCreated([
                  'status' => 201,
                  'message' => 'Data created.'
               ]);
            } else {
               return $this->fail($this->PetsModel->errors());
            }
         } catch (\Exception $e) {
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }



   public function getPetDetails($pet_id)
   {
      $pet = $this->PetsModel->select('pets.id, pets.owner_id, pets.name AS pName, pets.photo, pets.sex, pets.age, pets.weight, pets.weight_unit, pets.profile, breeds.id AS bId, breeds.id_specie, breeds.name AS bname, species.id AS sId, species.name AS spName, species.description')
         ->join('species', 'species.id = pets.specie_id')
         ->join('breeds', 'breeds.id = pets.breed_id')
         ->find($pet_id);
      if ($pet) {
         $owner = $this->OwnersModel->find($pet['owner_id']);
         $response = [
            'pet' => $pet,
            'owner' => $owner
         ];
         return $this->response->setJSON($response);
      } else {
         return $this->response->setJSON(['error' => 'Mascota no encontrada'], 404);
      }
   }


   public function show($id = null)
   {
      try {
         // Selección optimizada de datos
         $data = $this->PetsModel->select('pets.id, pets.owner_id, pets.name AS pName, pets.photo, pets.sex, pets.age, pets.weight, pets.weight_unit, pets.profile, breeds.id AS bId, breeds.id_specie, breeds.name AS bname, species.id AS sId, species.name AS spName, species.description, owners.id AS oId, owners.ic, owners.first_name, owners.last_name, owners.email, owners.address, owners.phone')
            ->join('owners', 'owners.id = pets.owner_id')
            ->join('species', 'species.id = pets.specie_id')
            ->join('breeds', 'breeds.id = pets.breed_id')
            ->find($id);

         if ($data) {
            // Construir la tabla con los datos obtenidos
            $table = '<table class="table table-sm activate-select dt-responsive nowrap w-100">';
            $table .= '<tr><th>Foto</th><td><div class="avatar"><img src="' . base_url('uploads/pets/') . $data['photo'] . '" class="avatar-img rounded-circle" width="60"></div></td></tr>';
            $table .= '<tr><th>Owner</th><td>' . $data['first_name'] . ' ' . $data['last_name'] . '</td></tr>';
            $table .= '<tr><th>Nombre</th><td>' . $data['pName'] . '</td></tr>';
            $table .= '<tr><th>Sexo</th><td>' . $data['sex'] . '</td></tr>';
            $table .= '<tr><th>Edad</th><td>' . $data['age'] . '</td></tr>';
            $table .= '<tr><th>Peso</th><td>' . $data['weight'] . ' ' . $data['weight_unit'] . '</td></tr>';
            $table .= '<tr><th>Especie</th><td>' . $data['spName'] . '</td></tr>';
            $table .= '<tr><th>Raza</th><td>' . $data['bname'] . '</td></tr>';
            $table .= '<tr><th>Profile</th><td>' . $data['profile'] . '</td></tr>';
            $table .= '</table>';

            // Enviar respuesta HTML como JSON
            return $this->respond($table);
         } else {
            return $this->failNotFound('No se encontraron datos para el ID proporcionado.');
         }
      } catch (\Exception $e) {
         return $this->failServerError('Lo siento, ocurrió un error. Por favor contacte al administrador.');
      }
   }


   public function edit($id = null)
   {
      try {
         $data = $this->PetsModel->find($id);

         if ($data) {
            $data = [
               'data_owners' => $this->OwnersModel->findAll(),
               'data_species' => $this->SpeciesModel->findAll(),
               'data_breeds' => $this->BreedsModel->findAll(),
               'data_pets' => $data
            ];

            echo view('admin/pets/form', $data);
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   public function update($id = null)
   {
      // Obtener los campos del formulario
      $request = [
         'owner_id' => $this->request->getPost('owner_id'),
         'name' => $this->request->getPost('name'),
         'sex' => $this->request->getPost('sex'),
         'age' => $this->request->getPost('age'),
         'weight' => $this->request->getPost('weight'),
         'weight_unit' => $this->request->getPost('weight_unit'),
         'specie_id' => $this->request->getPost('specie_id'),
         'breed_id' => $this->request->getPost('breed_id'),
         'profile' => $this->request->getPost('profile'),
         'created_at' => $this->request->getPost('created_at'),
         'updated_at' => date('Y-m-d H:i:s') // Actualizamos la fecha de actualización
      ];

      // Validar los datos
      $this->rules();

      if ($this->validation->run($request) != TRUE) {
         return $this->respond([
            'status' => 400,
            'error' => 400,
            'messages' => $this->validation->getErrors()
         ], 400);
      } else {
         try {
            // Verifica si se subió una nueva imagen
            $img = $this->request->getFile('photo');
            if ($img && $img->isValid() && !$img->hasMoved()) {
               // Generar un nombre único para la imagen
               $newName = $img->getRandomName();

               // Mover la imagen a la carpeta temporalmente
               $img->move('uploads/pets/original', $newName);

               // Redimensionar la imagen a 300x300 píxeles
               $this->imageService->withFile('uploads/pets/original/' . $newName);
               $this->imageService->resize(300, 300, true, 'height');
               $this->imageService->save('uploads/pets/' . $newName); // Guardar la imagen redimensionada

               // Eliminar la imagen original si existe (si estás actualizando una imagen previamente existente)
               $pet = $this->PetsModel->find($id); // Obtener los datos actuales de la mascota
               if ($pet && !empty($pet['photo'])) {
                  $oldPhotoPath = 'uploads/pets/' . $pet['photo'];
                  if (file_exists($oldPhotoPath)) {
                     unlink($oldPhotoPath); // Borrar la imagen anterior
                  }
               }

               // Borrar la imagen original subida temporalmente
               unlink('uploads/pets/original/' . $newName);

               // Actualizar el nombre de la imagen en la base de datos
               $request['photo'] = $newName;
            }

            // Actualizar los datos en la base de datos
            $update = $this->PetsModel->update($id, $request);

            if ($update) {
               return $this->respondNoContent('Data updated');
            } else {
               return $this->fail($this->PetsModel->errors());
            }
         } catch (\Exception $e) {
            return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
         }
      }
   }


   public function delete($id = null)
   {
      try {
         $data = $this->PetsModel->find($id);
         if ($data) {
            $this->PetsModel->delete($id);
            return $this->respondDeleted([
               'status' => 200,
               'message' => 'Data deleted.'
            ]);
         } else {
            return $this->failNotFound();
         }
      } catch (\Exception $e) {
         // return $this->failServerError($e->getMessage());
         return $this->failServerError('Sorry, an error occurred. Please contact the administrator.');
      }
   }

   private function rules()
   {
      $this->validation->setRules([
         'owner_id' => [
            'label' => 'Owner Id',
            'rules' => 'required|numeric'
         ],
         'name' => [
            'label' => 'Name',
            'rules' => 'required|string|max_length[50]'
         ],
         'sex' => [
            'label' => 'Sex',
            'rules' => 'required|in_list[male, female]'
         ],
         'age' => [
            'label' => 'Age',
            'rules' => 'required'
         ],
         'weight' => [
            'label' => 'Weight',
            'rules' => 'required|decimal|max_length[5]'
         ],
         'specie_id' => [
            'label' => 'Specie Id',
            'rules' => 'required|numeric'
         ],
         'breed_id' => [
            'label' => 'Breed Id',
            'rules' => 'required|numeric'
         ],
         'profile' => [
            'label' => 'Profile',
            'rules' => 'required|string'
         ]
      ]);
   }
}
