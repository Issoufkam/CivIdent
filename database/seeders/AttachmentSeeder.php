<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Document;
use Illuminate\Database\Seeder;

class AttachmentSeeder extends Seeder
{
    public function run(): void
    {
        $documents = Document::all();

        foreach ($documents as $doc) {
            Attachment::create([
                'path' => 'attachments/doc_' . $doc->id . '.pdf',
                'mime_type' => 'application/pdf',
                'document_id' => $doc->id,
            ]);
        }
    }
}
