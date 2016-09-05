<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests;
use Exception;
use App\HttpStatusCode;
use App\File;

class FileController extends Controller
{
    /**
     * @SWG\Get(path="/file",
     *   tags={"Get Files"},
     *   summary="Get list file avaiable",
     *   description="",
     *   operationId="listFiles",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Response(
     *         response=200,
     *         description="Success"
     *     ),
     *   @SWG\Response(
     *         response=503,
     *         description="File list is empty",
     *   )
     * )
     */
    public function index(Request $request) 
    {
        try {
            $files = File::orderBy('created_at', 'desc')->get();
            
            return $this->response($files);
            
        } catch (Exception $ex) {
            return $this->response($ex->getMessage(), $ex->getCode(), 
                    HttpStatusCode::SERVICE_UNAVAILABLE);
        }
    }
    
    /**
     * @SWG\Get(path="/file/download/{originalName}",
     *   tags={"Download file"},
     *   summary="Download file with original name",
     *   description="",
     *   operationId="downloadFile",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="path",
     *     type="string",
     *     name="originalName",
     *     description="File orignal name",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   @SWG\Response(response=503,  description="File not found")
     * )
     */
    public function download(Request $request, $originalName= '') 
    {
        try {
            $file = File::where('original_name', $originalName)->first();
            if (! $file) {
                throw new Exception('File ' . $originalName . ' not found');
            }
            
            $path = __DIR__ . '/../../../storage/app/file/' . $file->hash_name;
            
            return response()->download($path, 
                $file->original_name,[
                    'Content-Type: ' . $file->content_type,
                ]
            );
            
        } catch (Exception $ex) {
            return $this->response($ex->getMessage(), $ex->getCode(), 
                    HttpStatusCode::SERVICE_UNAVAILABLE);
        }
    }
    
    /**
     * @SWG\Post(path="/file/upload",
     *   tags={"Upload file"},
     *   summary="Upload single file",
     *   description="",
     *   operationId="uploadFile",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="formData",
     *     type="file",
     *     name="myfile",
     *     description="Browse a file to upload",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   @SWG\Response(response=503,  description="Server error")
     * )
     */
    public function upload(Request $request)
    {
        try {
            if (! $request->hasFile('myfile')) {
                throw new Exception('File not found', 1);
            } 
            $myfile = $request->myfile;
            
            $originalName = $myfile->getClientOriginalName();
            $hashName = $myfile->hashName();
            $ext = $myfile->getClientOriginalExtension();
            $max = File::max('id') + 1;
            
            $file = File::where('hash_name', $hashName)->first();
            if (! $file) {
                $myfile->store('file');
            }
            
            //save file to db
            $file = new File();
            $file->original_name = 
                    str_replace("." . $ext, "(" . $max . ")." .$ext, $originalName);
            $file->hash_name = $hashName;
            $file->content_type = $myfile->getMimeType();
            $file->save();
            
            return $this->response([
                'message' => 'Upload successfully file ' . $originalName,
                'original_name' => $file->original_name
            ]);
            
        } catch (Exception $ex) {
            return $this->response($ex->getMessage(), $ex->getCode(), HttpStatusCode::SERVICE_UNAVAILABLE);
        }
    }
    
    /**
     * @SWG\Delete(path="/file/delete/{originalName}",
     *   tags={"Delete file"},
     *   summary="Delete a file storage on server.",
     *   description="",
     *   operationId="Delete file",
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     in="path",
     *     type="string",
     *     name="originalName",
     *     description="File name to delete",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Successful operation",
     *   ),
     *   @SWG\Response(response=503,  description="Server error")
     * )
     */
    public function delete(Request $request, $originalName = '')
    {
        try {
            $file = File::where('original_name', $originalName)->first();
            if (! $file) {
                throw new Exception('File not found');
            }
            $count = File::where('hash_name', $file->hash_name)-> count();
            
            //file is unique
            if ($count == 1) {
                //delete in db
                $file->delete();
                
                //then delete storage file
                $path = 'file/' . $file->hash_name;
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }
            //file is doublicated
            else {
                //delete in db
                $file->delete();
            }
            
            return $this->response(['message' => 'Delete successfull']);
            
        } catch (Exception $ex) {
            return $this->response($ex->getMessage(), 
                $ex->getCode(), 
                HttpStatusCode::SERVICE_UNAVAILABLE);
        }
    }
}
