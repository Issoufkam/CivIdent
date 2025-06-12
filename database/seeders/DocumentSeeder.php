<?php

namespace Database\Seeders;

use App\Models\Commune;
use App\Models\Document;
use App\Models\User;
use App\Enums\DocumentType;
use App\Enums\DocumentStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $communes = Commune::all();
        $citoyens = User::where('role', 'citoyen')->get();

        foreach ($communes as $commune) {
            foreach (DocumentType::cases() as $type) {
                for ($i = 0; $i < 5; $i++) {
                    $citoyen = $citoyens->random();

                    $metadata = $this->generateMetadata($type->value);

                    $registryNumber = now()->year . '-' . Str::upper($commune->code) . '-' . rand(1000, 9999);
                    $filename = 'doc-' . uniqid() . '.pdf';
                    $justificatifPath = $this->createFakePdf($filename, $metadata);

                    Document::create([
                        'type' => $type->value,
                        'status' => DocumentStatus::APPROUVEE,
                        'registry_number' => $registryNumber,
                        'metadata' => $metadata,
                        'justificatif_path' => $justificatifPath,
                        'user_id' => $citoyen->id,
                        'commune_id' => $commune->id,
                        'agent_id' => null,
                    ]);
                }
            }
        }
    }

    private function generateMetadata(string $type): array
    {
        return match ($type) {
            'naissance' => [
                'nom' => fake()->lastName(),
                'prenom' => fake()->firstName(),
                'date_acte' => fake()->date('Y-m-d'),
                'nom_pere' => fake()->name('male'),
                'nom_mere' => fake()->name('female')
            ],
            'deces' => [
                'nom' => fake()->lastName(),
                'prenom' => fake()->firstName(),
                'date_acte' => fake()->date('Y-m-d'),
                'cause' => 'Maladie naturelle'
            ],
            default => [
                'objet' => 'Demande de document de type ' . $type,
                'date_acte' => fake()->date('Y-m-d')
            ]
        };
    }

    private function createFakePdf(string $filename, array $metadata): string
    {
        $html = view('pdf.fake', ['metadata' => $metadata])->render();
        $pdf = Pdf::loadHTML($html);
        Storage::put("justificatifs/{$filename}", $pdf->output());

        return "justificatifs/{$filename}";
    }
}
