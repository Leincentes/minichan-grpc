<?php
declare(strict_types=1);

namespace Minichan\Grpc;

use Minichan\Exception\GRPCException;

class BaseStub
{
    private $client;
    private $deserialize;
    private $streamId;

    public function __construct($client)
    {
        $this->client = $client ?: null;
    }

    /**
     * Perform a simple gRPC request.
     *
     * @param string $method
     * @param mixed $request
     * @param array $metadata
     * @param array $deserialize
     *
     * @return mixed
     *
     * @throws GRPCException
     */
    protected function _simpleRequest($method, $request, $deserialize, array $metadata = [])
    {
        $streamId = $this->client->send($method, $request);
        [$data, $trailers] = $this->client->recv($streamId);

        $this->checkResponseStatus($trailers);

        return $this->_deserializeResponse($deserialize, $data);
    }

    /**
     * Deserialize the gRPC response.
     *
     * @param array $deserialize
     * @param mixed $value
     *
     * @return mixed
     */
    protected function _deserializeResponse($deserialize, $value)
    {
        if ($value === null) {
            return;
        }

        [$className, $deserializeFunc] = $deserialize;
        $obj = new $className();
        $obj->mergeFromString($value);

        return $obj;
    }

    /**
     * Perform a gRPC request with server streaming.
     *
     * @param string $method
     * @param mixed $request
     * @param array $metadata
     * @param array $deserialize
     *
     * @return mixed
     */
    protected function _serverStreamRequest($method, $request, $deserialize, array $metadata = [])
    {
        $this->deserialize = $deserialize;
        $streamId = $this->client->send($method, $request);
        [$data] = $this->client->recv($streamId);

        $this->streamId = $streamId;

        return $this->_deserializeResponse($deserialize, $data);
    }

    /**
     * Get data from the server stream.
     *
     * @return mixed
     */
    protected function _getData()
    {
        [$data] = $this->client->recv($this->streamId);

        return $this->_deserializeResponse($this->deserialize, $data);
    }

    /**
     * Check the gRPC response status and throw an exception if it's an error.
     *
     * @param array $trailers
     *
     * @throws GRPCException
     */
    private function checkResponseStatus(array $trailers)
    {
        if ($trailers['grpc-status'] !== '0') {
            throw new GRPCException($trailers['grpc-message']);
        }
    }
}
