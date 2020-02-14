#!/usr/bin/php
<?php
namespace PatricPoba\MtnMomo;

require_once 'vendor/autoload.php';

use PatricPoba\MtnMomo\Http\GuzzleClient;

  
class SandboxUserProvision
{
    /**
     * Returns a UUID4 string.
     *
     * @return string uuid4
     */
    public static function uuid()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }
    
    public function provisionUser($host = null, $apiKey = null)
    {
        if (is_null($host)) {
            echo 'providerCallbackHost:';
            $host = fgets(STDIN); 
        }

        if (is_null($apiKey)) {
            echo 'Ocp-Apim-Subscription-Key: ';
            $apiKey = fgets(STDIN); 
        }

        $data = json_encode(array("providerCallbackHost" => trim($host)));

        $url = 'https://sandbox.momodeveloper.mtn.com/v1_0/apiuser';

        echo $token = static::uuid();
        $ch = curl_init();

        $userUrl = "https://sandbox.momodeveloper.mtn.com/v1_0/apiuser/" . $token . "/apikey";

        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "post");

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        //curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HEADER,false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array('Content-Type: application/json',
                'X-Reference-Id: ' . $token,
                'Accept: application/json',
                'Ocp-Apim-Subscription-Key: ' . trim($apiKey)
            )
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        if ($result) {
            curl_setopt($ch, CURLOPT_URL, $userUrl);

            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array('Content-Type: application/json',
                    'Accept: application/json',
                    'Ocp-Apim-Subscription-Key: ' . trim($apiKey)
                )
            );


            $result2 = curl_exec($ch);


            curl_close($ch);
            echo $result;
            echo $result2;
            $res = json_decode($result2, true);


            echo "Your User Id and API secret : {UserId:" . $token . " , APISecret: " . $res["apiKey"] . " }";
        }
        else{
            var_dump($result);
            echo "something went wrong";
        }
    }


    public function provisionSandboxUser( $primaryKey = null, $providerCallbackHost = null, $xReferenceId = null)
    {
        if (is_null($primaryKey)) {
            echo 'Ocp-Apim-Subscription-Key (primaryKey) : ';
            $primaryKey = fgets(STDIN);
        }

        if (is_null($providerCallbackHost)) {
            echo 'providerCallbackHost (callback domain) : ';
            $providerCallbackHost = fgets(STDIN);
        }

        if (is_null($xReferenceId) ){
            $xReferenceId = static::uuid(); 
        }
 
        $headers = [
            'X-Reference-Id'            =>  $xReferenceId,
            'Content-Type'              => 'application/json',
            'Ocp-Apim-Subscription-Key' => $primaryKey,
        ];
        $params = [ 'providerCallbackHost' => $providerCallbackHost ];
   
        try {
            // Create a sandbox user
            $response = (new GuzzleClient())
                ->request('post', 'https://sandbox.momodeveloper.mtn.com/v1_0/apiuser', $params, $headers);
            var_dump($response);
    
            // Create an apiKey
            // $response = (new GuzzleClient())
                // ->request('post', 'https://sandbox.momodeveloper.mtn.com/v1_0/apiuser/'. $xReferenceId . '/apikey', [], $headers);

            // echo "Your credentials: { Ocp-Apim-Subscription-Key: {$primaryKey} , UserId (X-Reference-Id): " . $xReferenceId . " , APISecret: " . $response->apiKey  .' }';

        } catch (\Exception $exception) {
            echo $exception->getMessage();
            throw $exception;
            return;
        } 
    }
}
if (!debug_backtrace()) {
    // $provision = new SandboxUserProvision();
    // $provision->provisionUser();

    (new SandboxUserProvision)->provisionSandboxUser('7f4c5b93d101446faad39e0eb1eb932c', 'afdf.dsfdfda.com');

    // echo $uuid = SandboxUserProvision::uuid() ;
    
    // echo ' count:' . strlen($uuid);
    // var_dump($uuid);
}

