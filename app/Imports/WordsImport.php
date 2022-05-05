<?php

namespace App\Imports;

use App\Models\Word;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WordsImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    private $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function collection(Collection $collection)
    {
        foreach ($collection as $item) {
            if (isset($item['eng']) && $item['eng'] !== null) {
                Word::firstOrCreate(
                    [
                        'eng' => $item['eng'],
                        'rus' => $item['rus'],
                        'dictionary_id' => $this->id,
                    ]
                );
            }
        }
    }
}
