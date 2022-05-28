<?php

namespace App\Http\Controllers;

use App\Imports\CsvWordsImport;
use App\Imports\WordsImport;
use App\Models\Dictionary;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use ZanySoft\Zip\Zip;

class MainController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function store(Request $request)
    {
        try {
            ini_set('memory_limit', '-1');
            $fileOriginalName = $request->file('table')->getClientOriginalName();
            $fileName = preg_replace('/\..+/', '', $fileOriginalName);
            $fileType = preg_replace('/.+\./', '', $fileOriginalName);
            $wordList = Dictionary::create([
                'name' => $fileName,
            ]);

            if ($request->file('images')) {
                $zip = new \ZipArchive();
                $result = $zip->open($request->file('images'));

                if ($result) {
                    $res = $zip->extractTo($_SERVER['DOCUMENT_ROOT'] . '/storage/app/public/images');
                    $zip->close();
                }

            }

            if ($fileType === 'csv') {
                Excel::import(new CsvWordsImport($wordList->id), $request->file('table'), null, \Maatwebsite\Excel\Excel::CSV);
            } else {
                Excel::import(new WordsImport($wordList->id), $request->file('table'));
            }

            return $wordList;

        } catch (\Exception $ex) {
            $wordList->delete();
            return abort(500);
        }
    }

    public function throwDictionaries()
    {
        $dictionaries = Dictionary::all();
        return $dictionaries;
    }

    public function deleteDictionary(Dictionary $dictionary)
    {
        $dictionary->delete();
        return 'Success!';
    }

    public function throwDictionariesAndWords()
    {
        $dictionaries = Dictionary::all();

        foreach ($dictionaries as $dictionary) {
            $dictionary['words'] = $dictionary->words;
        }

        return $dictionaries;
    }

    public function throwWordsFromDictionary(Dictionary $dictionary)
    {
        $words = $dictionary->words;
        return $words;
    }
}
