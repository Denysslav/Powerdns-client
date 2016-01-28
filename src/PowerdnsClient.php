<?php

/**
 * Created by IntelliJ IDEA.
 * User: Denislav
 * Date: 28.1.2016 г.
 * Time: 12:49
 */

namespace PowerdnsClient;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;

class Client {

    /**
     * @var \GuzzleHttp\Client Instance of the Guzzle Http client
     */
    protected $guzzleClient;

    /**
     * @var string $uri Uri segment added to base_uri in requests
     */
    private $uri;

    public function __construct($baseUri, $headers, $options = null)
    {
        $this->guzzleClient = new GuzzleClient(['base_uri' => $baseUri, 'headers' => $headers]);
        $this->uri = 'servers/localhost/';
    }

    /**
     * Create zone in PowerDNS server
     *
     * @param mixed[] $data Data passed by user
     *
     * @return mixed[]|string[] Populated data|Exception message
     *
     */
    public function createZone(array $data)
    {
        $this->uri .= 'zones';
        try {
            $response = $this->guzzleClient->request('POST', $this->uri, ['body' => json_encode($data)]);
        } catch(ClientException $e){
            return $this->handleExceptions($e);
        } catch(ConnectException $e){
            return $this->handleExceptions($e);
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * Fetch DNS zone with it's records
     *
     * @param int $id PowerDNS id of zone
     *
     * @return mixed[]|string[] Data corresponding to $id|Exception message
     *
     */
    public function getZone($id)
    {
        $this->uri .= 'zones/' . $id;

        try {
            $response = $this->guzzleClient->get($this->uri);
        } catch(ClientException $e){
            return $this->handleExceptions($e);
        } catch(ConnectException $e){
            return $this->handleExceptions($e);
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * Update basic zone data
     *
     * @param int $id PowerDNS id of zone
     *
     * @param mixed[] $data Data to be replaced in zone
     *
     * @return mixed[]|string[] Updated basic zone data|Exception message
     *
     */
    public function updateZone($id, array $data)
    {
        $current = $this->getZone($id);
        $merged  = array_merge($current, $data);
        $this->uri .= 'zones/' . $id;
        try {
            $response = $this->guzzleClient->request('PUT', $this->uri, ['body' => json_encode($merged)]);
        } catch(ClientException $e){
            return $this->handleExceptions($e);
        } catch(ConnectException $e){
            return $this->handleExceptions($e);
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * Delete a zone
     *
     * @param int $id PowerDNS id of zone
     *
     * @return string|string[] Success message|Exception message
     *
     */
    public function deleteZone($id)
    {
        $this->uri .= 'zones/' . $id;
        try {
            $response = $this->guzzleClient->delete($this->uri);
        } catch(ClientException $e){
            return $this->handleExceptions($e);
        } catch(ConnectException $e){
            return $this->handleExceptions($e);
        }

        return 'Zone: ' . $id . ' has been deleted';
    }

    /**
     * Modify present RRsets and comments
     *
     * @param int $zoneId PowerDNS id of zone
     *
     * @param mixed[] $data Data to be inserted as record.
     *                      If 'changetype' key is set to 'REPLACE'
     *                      all rrsets matching 'name' and 'type'
     *                      will be replaced
     *
     * @return mixed[]|string[] Updated rrset data|Exception message
     *
     */
    public function patchRecord($zoneId, array $data)
    {
        $this->uri .= 'zones/' . $zoneId;

        try {
            $response = $this->guzzleClient->request('PATCH', $this->uri, ['body' => json_encode($data)]);
        } catch(ClientException $e){
            return $this->handleExceptions($e);
        } catch(ConnectException $e){
            return $this->handleExceptions($e);
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * Delete present RRsets and comments
     *
     * @param int $zoneId PowerDNS id of zone
     *
     * @param mixed[] $data If 'changetype' key is set to 'DELETE'
     *                       all rrsets matching 'name' and 'type'
     *                      will be deleted
     *
     * @return mixed[]|string[] Deleted rrset data|Exception message
     */
    public function deleteRecord($zoneId, array $data)
    {
        $this->uri .= 'zones/' . $zoneId;

        try {
            $response = $this->guzzleClient->request('PATCH', $this->uri, ['body' => json_encode($data)]);
        } catch(ClientException $e){
            return $this->handleExceptions($e);
        } catch(ConnectException $e){
            return $this->handleExceptions($e);
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * Handle various Guzzle exceptions
     *
     * @param ConnectException|ClientException $exception Guzzle exception instance
     *
     * @return mixed[] $messages Array of errorCodes and messages
     */
    protected function handleExceptions($exception)
    {
        $messages = array();
        if($exception instanceof ConnectException ||
            $exception instanceof ClientException){
            $messages = [
                'errorCode' => $exception->getCode(),
                'message' => json_decode($exception->getResponseBodySummary($exception->getResponse()), true)['error']
            ];
        } else {

        }

        return $messages;
    }
}