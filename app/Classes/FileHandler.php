<?php

namespace App\Classes;

use Storage;
use DB;
use App\Models\FileHandle;
use File;
use Response;

class FileHandler
{
    private $disk;

    public function __construct()
    {
        $this->disk = 'local';
    }

    public function test()
    {
        dd(1);
    }

    public function disk($disk)
    {
        $this->disk = $disk;
        return $this;
    }

    /**
     * store
     *
     * @param  String $directory_path
     * @param  FileContent $file_contents
     * @param  String $filename
     * @return Mixed
     */
    public function store($directory_path, $file_contents, FileHandle $file)
    {
        $directory_path = rtrim($directory_path, '/'); // remove trailing slash
        $full_file_path = $directory_path . '/' . $file->filename;


        $success = Storage::disk($this->disk)->put($full_file_path, $file_contents);
        $file->filepath = $full_file_path;
        $file->disk = $this->disk;
        $file->uid = auth()->user()->id ?? 0; // default 0, some time call from api

        if ($success) {
            $file_handle = FileHandle::create($file->toArray());
            return $file_handle->fid;
        } else {
            return false;
        }
    }

    /**
     * store_file_from_url
     *
     * @param  String $directory_path
     * @param  String $url
     * @param  mixed $filename not required, without extension
     * @return void
     */
    public function store_from_url($directory_path, $url, $filename = null)
    {
        $file_contents = file_get_contents($url);
        $file_info = $this->get_file_info_from_url($url);
        //custom file name
        if ($filename != null) {
            $file_info->filename = $filename . '.' . $file_info->file_ext;
        }
        return $this->store($directory_path, $file_contents, $file_info);
    }

    public function store_from_request($directory_path, $request_name, $filename = null, $temp = false)
    {

        //TODO check file valid ##
        if (!\Request::hasFile($request_name)) {
            //TODO return some error
        }

        $file = \Request::file($request_name);

        $file_info = $this->get_file_info_from_request($file);

        //custom file name
        if ($filename != null) {
            $file_info->filename = $filename . '.' . $file_info->file_ext;
        }
        if ($temp) {
            $file_info->status = 0;
        }
        $file_contents =  $file->get();

        return $this->store($directory_path, $file_contents, $file_info);
    }

    /*
        For url file use
    */
    static function get_file_info_from_url($url)
    {
        $info = pathinfo($url);
        $info['filename'] = $info['filename'] . '.' . $info['extension'];
        $info['file_ext'] = $info['extension'];

        //get Mine Type
        $contents = file_get_contents($url);
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $info['filemine'] = $finfo->buffer($contents);

        //get File size
        $info['filesize'] = strlen($contents);
        $file = new FileHandle($info);
        return $file;
    }

    static function get_file_info_from_request(\Illuminate\Http\UploadedFile $file)
    {
        $info['filename'] = $file->getClientOriginalName() . '.' . $file->extension();
        $info['file_ext'] = $file->extension();
        $info['filemine'] = $file->getClientMimeType();
        $info['filesize'] = $file->getSize();
        $file = new FileHandle($info);
        return $file;
    }

    function remove_file_by_fid($fid)
    {
        $file = FileHandle::find($fid);
        if ($file == null) return false;

        $disk = $file->disk;
        $path = $file->filepath;
        Storage::disk($disk)->delete($path);
        FileHandle::find($fid)->delete();
        return true;
    }

    function get_file($fid, $placeholder = false)
    {
        $file = FileHandle::find($fid);
        if ($file != null) {
            if ($file->disk == 'local') {
                $path = storage_path('app/' . $file->filepath);
                if (!File::exists($path)) {
                    abort(404);
                }
            }
        } else { // if file not found, and required placeholder, show a placeholder
            if ($placeholder) {
                $path = public_path('assets\photo\default\person.jpg');
            } else {
                abort(404);
            }
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200)->header("Content-Type", $type);
        return $response;
    }

    //only accessibly by same user
    function get_temp_file($fid, $placeholder = false)
    {
        //check uid
        $file = FileHandle::find($fid);
        if (\Auth::check() && $file->uid == \Auth::user()->id) {
            return $this->get_file($fid, $placeholder);
        } else {
            abort(404);
        }
    }

    public function get_file_url($fid)
    {
        if (\Auth::check()) {
            if (\Auth::user()->hasRole('admin')) {
                return url('/admin/view_file/' . $fid);
            } elseif (\Auth::user()->hasRole('member')) {
                return url('/member/view_file/' . $fid);
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public function get_temp_file_url($fid)
    {
        return url('/view_temp_file/' . $fid);
    }

    public function set_model_info($fid, $model_name, $model_primary_key, $model_field_name_for_fid)
    {
        FileHandle::find($fid)
            ->fill([
                'model_name' => $model_name,
                'model_primary_key' => $model_primary_key,
                'model_field_name_for_fid' => $model_field_name_for_fid,
            ])
            ->save();
    }

    public function remove_file_if_exists($model_name, $model_primary_key, $model_field_name_for_fid)
    {
        $files = FileHandle::where('model_name', $model_name)
            ->where('model_primary_key', $model_primary_key)
            ->where('model_field_name_for_fid', $model_field_name_for_fid)
            ->get();

        foreach ($files as $f) {
            FileHandle::find($f->fid)->delete();
            $this->remove_file_by_fid($f->fid);
        }
    }
}
