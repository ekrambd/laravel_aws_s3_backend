<?php
 namespace App\Repositories\File;
 use App\Models\File;
 use App\Models\Bucket;
 use Aws\S3\S3Client;
 use Illuminate\Support\Str;
 use Validator;

 class FileRepository implements FileInterface
 {  

 	protected $awsKey;
 	protected $awsSecret;
 	protected $awsRegion;

 	public function __construct()
 	{
 		$this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => config('services.ses.region'),
            'credentials' => [
                'key' => config('services.ses.key'),
                'secret' => config('services.ses.secret'),
            ],
        ]);
 	}

 	public function fetch()
 	{
 		try 
 		{
 			$files = File::query();
 			return $files;
 		}catch(Exception $e){
 			return response()->json(['status' => false, 'code' => $e->getCode(), 'message' => $e->getMessage()],500);
 		}
 	}

 	public function store($request)
 	{   
 		//ini_set('max_execution_time', 600);
 		// $bucket = bucket($request->bucket_id);
        // $bucketName = $bucket->bucket_slug;
        // $storageClass = $request->input('storage_class', 'STANDARD');
        // $file = $request->file('file');
        // $filePath = $file->getRealPath();
        // $fileSize = filesize($filePath);
        // $fileMime = $file->getMimeType();
        // $folder = folder($request->folder_id); 
        // $key = $folder?$folder->folder_slug.uniqid() . '_' . $file->getClientOriginalName():uniqid() . '_' . $file->getClientOriginalName();
        // $acl = $request->status === 'Public' ? 'public-read' : 'private';

        // try {
        //     // Initiate multipart upload
        //     $create = $this->s3Client->createMultipartUpload([
        //         'Bucket' => $bucketName,
        //         'Key' => $key,
        //         'ContentType' => $fileMime,
        //         'StorageClass' => $storageClass,
        //         'ACL' => $acl,
        //         'Tagging' => http_build_query([
        //             'Public' => $request->status === 'Public' ? 'true' : 'false'
        //         ]),
        //     ]);

        //     $uploadId = $create['UploadId'];
        //     $partSize = 5 * 1024 * 1024; // 5MB
        //     $parts = [];
        //     $partNumber = 1;

        //     $fileStream = fopen($filePath, 'rb');

        //     while (!feof($fileStream)) {
        //         $data = fread($fileStream, $partSize);

        //         $uploadPart = $this->s3Client->uploadPart([
        //             'Bucket' => $bucketName,
        //             'Key' => $key,
        //             'UploadId' => $uploadId,
        //             'PartNumber' => $partNumber,
        //             'Body' => $data,
        //         ]);

        //         $parts[] = [
        //             'PartNumber' => $partNumber,
        //             'ETag' => $uploadPart['ETag'],
        //         ];

        //         $partNumber++;
        //     }

        //     fclose($fileStream);

        //     // Complete the multipart upload
        //     $result = $this->s3Client->completeMultipartUpload([
        //         'Bucket' => $bucketName,
        //         'Key' => $key,
        //         'UploadId' => $uploadId,
        //         'MultipartUpload' => ['Parts' => $parts],
        //     ]);

        //     return response()->json([
        //     	'status' => true,
        //         'message' => 'Multipart upload successful!',
        //         'file_key' => $key,
        //         'file_url' => $result['Location'],
        //         'visibility' => $request->status,
        //         'storage_class' => $storageClass,
        //     ]);

        // }catch (AwsException $e) {
        //     if (isset($uploadId)) {
        //         $this->s3Client->abortMultipartUpload([
        //             'Bucket' => $bucketName,
        //             'Key' => $key,
        //             'UploadId' => $uploadId,
        //         ]);
        //     }

        //     return response()->json([
        //     	'status' => false,
        //         'message' => 'Multipart upload failed: ' . $e->getMessage(),
        //     ], 500);
        // }

        $file = $request->file('file'); // Get the file chunk
        $fileName = $request->input('fileName'); // The name of the original file
        $currentChunk = $request->input('currentChunk'); // The current chunk number
        $totalChunks = $request->input('totalChunks'); // Total number of chunks

        // Define the temporary path where we will store the chunk
        $chunkPath = public_path('uploads/temp/' . $fileName . '.part' . $currentChunk);

        // Move the chunk to the temporary location
        $file->move(public_path('uploads/temp'), $fileName . '.part' . $currentChunk);

        // Check if all chunks have been uploaded
        if ($currentChunk == $totalChunks - 1) {
            // Combine all the chunks into the final file
            $this->combineChunks($fileName, $totalChunks);
        }

        return response()->json(['status' => 'Chunk uploaded successfully']);
 	}

    private function combineChunks($fileName, $totalChunks)
    {
        // Define the path to store the combined file
        $finalFilePath = public_path('uploads/' . $fileName);

        // Open the final file in append mode
        $finalFile = fopen($finalFilePath, 'ab');

        // Loop through all the chunks and append them to the final file
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkPath = public_path('uploads/temp/' . $fileName . '.part' . $i);

            // Open the chunk and read its contents
            $chunk = fopen($chunkPath, 'rb');
            while ($buffer = fread($chunk, 4096)) {
                fwrite($finalFile, $buffer); // Write the chunk to the final file
            }
            fclose($chunk); // Close the chunk file after reading
            unlink($chunkPath); // Delete the chunk file after merging
        }

        fclose($finalFile); // Close the final file
        return response()->json(['status' => 'File combined successfully']);
    }

 	public function statusUpdate($request)
 	{
 		try
 		{
 			//
 		}catch(Exception $e){
 			return response()->json(['status' => false, 'code' => $e->getCode(), 'message' => $e->getMessage()],500);
 		}
 	}

 	public function delete($file)
 	{
 		try
 		{
 			//
 		}catch(Exception $e){
 			return response()->json(['status' => false, 'code' => $e->getCode(), 'message' => $e->getMessage()],500);
 		}
 	}
 }