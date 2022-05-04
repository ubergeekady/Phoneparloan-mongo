<?php

namespace App\Models;

use App\Http\Controllers\Mobile\v1\Base\BaseController;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UnderwritingFile extends Eloquent
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $collection = 'underwriting_files';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','extension','mimetype', 'weblink','filepath','size','user_id'
    ];

    public static function storeFile($file, $path = 'import', $provider = 'local', $store = true)
    {
        //Create a Unique Upload name for each file.
        $sName_unique = time() . '_' . uniqid(rand()) . '.' . $file->getClientOriginalExtension();
        $sName_with_path = S3_WEBLINK.$sName_unique;
        //Store in Uploads database
        if ($store) {
            self::create([
                'name' => $sName_unique,
                'filepath' => $path,
                'extension' => $file->getClientOriginalExtension(),
                'mimetype' => $file->getClientMimeType(),
                'weblink' => $sName_with_path,
                'user_id' => \user('mobile')->id,
                'size' => $file->getClientSize(),
            ]);
        }
        //get file content from url
        $filePath = UNDERWRITING_JSON_FILE_FOLDER.$sName_unique;
        Storage::disk('s3')->put($filePath, file_get_contents($file));
        return array($sName_unique, $sName_with_path);
    }
}
