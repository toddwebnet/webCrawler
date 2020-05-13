<?php
/**
 * User: jtodd
 * Date: 2020-05-12
 * Time: 17:02
 */

namespace App\Services;

use App\Helpers\Utils;
use Aws\S3\S3Client;
use GuzzleHttp\Client;

class S3StorageService
{
    private $s3Client;
    private $bucket;

    public function __construct()
    {
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'endpoint' => env('AWS_S3_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);
        $this->bucket = env('AWS_BUCKET');
    }

    public function putObject($fileStream)
    {
        $key = $this->getRandomPathName();
        $response =  $this->s3Client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key, //add path here
            'Body' => $fileStream,
            'ACL' => 'public-read'
        ]);
        if (
            $response['ObjectURL'] &&
            strpos($response['ObjectURL'], $key)!==false
        ) {
            $response['key'] = $key;
        }else{
            throw new \Exception("S3 not saving right");
        }
        return $response;

    }


    public function getUrl($objectUrl)
    {
        $client = new Client();
        $res = $client->request('GET',$objectUrl);
        return $res->getBody();
    }

    public function getObject($objectUrl)
    {
        if (strpos(strtolower($objectUrl), 'http') === 0) {
            return $this->getUrl($objectUrl);
        }
        $retrive = $this->s3Client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $objectUrl
        ]);
        return $retrive['Body'];
    }

    private function getRandomPathName()
    {
        return sha1(
            Utils::randomChars(6) .
            time() .
            Utils::randomChars(6)
        );
    }

}
