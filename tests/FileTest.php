<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
Use Illuminate\Http\UploadedFile;
use App\File;

class FileTest extends TestCase
{
    use DatabaseTransactions;
    
    /**
     * Test feature uploa file
     */
    public function testGetListFile()
    {
        $response = $this->call('Get', '/api/file');
        $this->seeStatusCode(200);
        $data = json_decode($response->getContent(true), true);
        $this->assertArrayHasKey('id', $data['data'][0]);
    }
   
    /**
     * Test feature upload file
     */
    public function testUploadFile()
    {
        $path = __DIR__ . '/stub/' . 'sample.txt';
        $name = "sample.txt";
        
        $file = new UploadedFile($path, $name, filesize($path), 
                'text/plain', null, true);
        $response = $this->call('POST', '/api/file/upload', 
                [], [], ['myfile' => $file], ['Accept' => 'application/json']);

        $this->assertResponseOk();
        $content = json_decode($response->getContent());
        $this->assertObjectHasAttribute('original_name', $content->data);
        return $content->data->original_name;
    }
    
    /**
     * Test feature download file
     */
    public function testDownloadFile()
    {
        $file = factory(File::class)->create();
        
        $source = __DIR__ . '/stub/' . $file->original_name;
        $destination = __DIR__ . '/../storage/app/file/' . $file->hash_name;
        copy($source, $destination);
        
        
        $response = $this->call('Get', '/api/file/download/' . $file->original_name);        
        $this->assertResponseOk();
        $this->seeHeader('content-type', 'text/plain');
        
        unlink($destination);
    }
    
    /**
     * Test feature delete file
     */
    public function testDeleteFile()
    {
        $file = factory(File::class)->create();
        
        $source = __DIR__ . '/stub/' . $file->original_name;
        $destination = __DIR__ . '/../storage/app/file/' . $file->hash_name;
        copy($source, $destination);
        
        $response = $this->call('Delete', '/api/file/delete/' . $file->original_name);
        $this->assertResponseOk();
    }
    
}
