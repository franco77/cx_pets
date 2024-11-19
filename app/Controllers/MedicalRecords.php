<?php

namespace App\Controllers;

use App\Models\OwnersModel;
use App\Models\PetsModel;
use App\Models\PetTestsModel;
use App\Models\PrescriptionsModel;
use App\Models\PrescriptionDetailsModel;
use App\Models\TestDetailsModel;
use App\Models\VaccinesModel;
use App\Models\VaccineDetailsModel;
use App\Models\SpeciesModel;
use App\Models\BreedsModel;

class MedicalRecords extends BaseController
{

    public function __construct()
    {
        $this->OwnersModel = new OwnersModel();
        $this->PetsModel = new PetsModel();
        $this->PetTestsModel = new PetTestsModel();
        $this->PrescriptionsModel = new PrescriptionsModel();
        $this->VaccinesModel = new VaccinesModel();
        $this->PrescriptionDetailsModel = new PrescriptionDetailsModel();
        $this->TestDetailsModel = new TestDetailsModel();
        $this->VaccineDetailsModel = new VaccineDetailsModel();
        $this->SpeciesModel = new SpeciesModel(); // Cargar modelo de especies
        $this->BreedsModel = new BreedsModel(); // Cargar modelo de razas

    }

    public function show($petId)
    {
        // Obtener la información de la mascota
        $pet = $this->PetsModel->find($petId);

        if (!$pet) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("La mascota no existe.");
        }

        // Obtener la información del propietario
        $owner = $this->OwnersModel->find($pet['owner_id']);

        if (!$owner) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("El dueño no existe.");
        }

        // Obtener la especie y la raza de la mascota
        $species = $this->SpeciesModel->find($pet['specie_id']);
        $breed = $this->BreedsModel->find($pet['breed_id']);

        // Obtener las pruebas del animal
        $petTests = $this->PetTestsModel->where('pet_id', $petId)->findAll();
        foreach ($petTests as &$test) {
            $test['details'] = $this->TestDetailsModel->where('pet_tests_id', $test['id'])->findAll();
        }

        // Obtener las recetas del animal
        $prescriptions = $this->PrescriptionsModel->where('pet_id', $petId)->findAll();
        foreach ($prescriptions as &$prescription) {
            $prescription['details'] = $this->PrescriptionDetailsModel->where('prescription_id', $prescription['id'])->findAll();
        }

        // Obtener las vacunas del animal
        $vaccines = $this->VaccinesModel->where('pet_id', $petId)->findAll();
        foreach ($vaccines as &$vaccine) {
            $vaccine['details'] = $this->VaccineDetailsModel->where('vaccine_id', $vaccine['id'])->findAll();
        }

        // Estructurar los datos en el array de respuesta
        $data = [
            'owner' => $owner,
            'pet' => $pet,
            'species' => $species,
            'breed' => $breed,
            'tests' => $petTests,
            'prescriptions' => $prescriptions,
            'vaccines' => $vaccines,
            'title' => 'Historia Clínica',
        ];

        // Pasar los datos a la vista
        return view('admin/medical_records/index', $data);
    }



    public function generatePdf($petId)
    {
        // Obtener la información de la mascota
        $pet = $this->PetsModel->find($petId);

        if (!$pet) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("La mascota no existe.");
        }

        // Obtener la información del propietario
        $owner = $this->OwnersModel->find($pet['owner_id']);

        if (!$owner) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("El dueño no existe.");
        }

        // Obtener la especie y la raza de la mascota
        $species = $this->SpeciesModel->find($pet['specie_id']);
        $breed = $this->BreedsModel->find($pet['breed_id']);

        // Obtener las pruebas del animal
        $petTests = $this->PetTestsModel->where('pet_id', $petId)->findAll();
        foreach ($petTests as &$test) {
            $test['details'] = $this->TestDetailsModel->where('pet_tests_id', $test['id'])->findAll();
        }

        // Obtener las recetas del animal
        $prescriptions = $this->PrescriptionsModel->where('pet_id', $petId)->findAll();
        foreach ($prescriptions as &$prescription) {
            $prescription['details'] = $this->PrescriptionDetailsModel->where('prescription_id', $prescription['id'])->findAll();
        }

        // Obtener las vacunas del animal
        $vaccines = $this->VaccinesModel->where('pet_id', $petId)->findAll();
        foreach ($vaccines as &$vaccine) {
            $vaccine['details'] = $this->VaccineDetailsModel->where('vaccine_id', $vaccine['id'])->findAll();
        }

        // Estructurar los datos en el array de respuesta
        $data = [
            'owner' => $owner,
            'pet' => $pet,
            'species' => $species,
            'breed' => $breed,
            'tests' => $petTests,
            'prescriptions' => $prescriptions,
            'vaccines' => $vaccines,
            'title' => 'Historia Clínica',
        ];

        // Cargar la vista con los datos
        $html = view('admin/medical_records/pdf', $data);

        // Inicializar Dompdf con opciones
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);

        // Configurar tamaño de papel y orientación
        $dompdf->setPaper('A4', 'portrait');

        // Renderizar el PDF
        $dompdf->render();

        // Descargar el archivo PDF
        $dompdf->stream('historia_clinica_' . $owner['first_name'] . '_' . $owner['last_name'] . '.pdf', ['Attachment' => true]);
    }
}
