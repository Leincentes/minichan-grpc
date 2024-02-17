<?php

declare(strict_types=1);
namespace Minichan\Grpc;

use Minichan\Config\Constant;
use Minichan\Exception\ClientException;
use Swoole\Coroutine;

class Client implements ClientInterface
{
    private $client;
    private $streams;
    private $closed = false;
    private $mode;
    private $settings = [
        'timeout'                      => Constant::TIMEOUT,
        'open_eof_check'               => Constant::OPEN_EOF_CHECK,
        'package_max_length'           => Constant::PACKAGE_MAX_LENGTH,
        'http2_max_concurrent_streams' => Constant::HTTP2_MAX_CONCURRENT_STREAMS,
        'http2_max_frame_size'         => Constant::HTT2_MAX_FRAME_SIZE,
        'max_retries'                  => Constant::MAX_RETRIES,
    ];

    public function __construct($host, $port, $mode = Constant::GRPC_CALL)
    {
        // Initialize Swoole HTTP2 Client
        $client = new \Swoole\Coroutine\Http2\Client($host, $port);
        $this->client = $client;
        $this->streams = [];
        $this->mode = $mode;
    }

    public function set(array $settings): self
    {
        // Merge additional settings with default settings
        $this->settings = array_merge($this->settings, $settings ?? []);
        return $this;
    }

    /**
     * Establish a connection to the remote endpoint
     */
    public function connect(): self
    {
        // Set client settings and start receiving data in a coroutine
        $this->client->set($this->settings);
        if (!$this->client->connect()) {
            throw new ClientException(Util::getErrorMessage($this->client->errCode, 9) . " {$this->client->host}:{$this->client->port}", $this->client->errCode);
        }

        Coroutine::create(function () {
            while (!$this->closed && [$streamId, $data, $pipeline, $trailers] = $this->recvData()) {
                // Handle received data based on the streamId and pipeline flag
                if ($streamId > 0 && !$pipeline) {
                    $this->streams[$streamId][0]->push([$data, $trailers]);
                    $this->streams[$streamId][0]->close();
                    unset($this->streams[$streamId]);
                } elseif ($streamId > 0) {
                    $this->streams[$streamId][0]->push([$data, $trailers]);
                }
            }
        });

        return $this;
    }

    /**
     * Get the stats of the client
     */
    public function stats(): array
    {
        return $this->client->stats();
    }

    /**
     * Close the connection to the remote endpoint
     */
    public function close()
    {
        $this->closed = true;
        $this->client->close();
    }

    /**
     * Send a message to the remote endpoint
     *
     * @param mixed $method
     * @param mixed $message
     * @param mixed $type
     *
     * @return bool|int
     *
     * @throws ClientException
     */
    public function send($method, $message, $type = 'proto', $user_agent = 'minichan/v1')
    {
        $isEndStream = $this->mode === Constant::GRPC_CALL;
        $retry = 0;

        // Retry sending the message with backoff
        while ($retry++ < $this->settings['max_retries']) {
            $streamId = $this->sendMessage($method, $message, $type, $user_agent);

            if ($streamId && $streamId > 0) {
                $this->streams[$streamId] = [new Coroutine\Channel(1), $isEndStream];
                return $streamId;
            }

            // Handle errors and wait before retrying
            if ($this->client->errCode > 0) {
                throw new ClientException(Util::getErrorMessage($this->client->errCode, 9) . " {$this->client->host}:{$this->client->port}", $this->client->errCode);
            }

            Util::usleep(10000);
        }

        return false;
    }

    /**
     * Receive data from a stream in the established connection based on streamId.
     *
     * @param mixed $streamId
     * @param mixed $timeout
     *
     * @return mixed
     */
    public function recv($streamId, $timeout = -1)
    {
        return $this->streams[$streamId][0]->pop($timeout);
    }

    /**
     * Push a message to the remote endpoint (used in client-side streaming mode).
     *
     * @param mixed $streamId
     * @param mixed $message
     * @param mixed $type
     * @param bool $end
     *
     * @return bool
     */
    public function push($streamId, $message, $type = 'proto', $end = false)
    {
        // Serialize the message based on the type
        if ($type === 'proto') {
            $payload = $message->serializeToString();
        } elseif ($type === 'json') {
            $payload = $message;
        }

        // Pack the payload and write it to the client
        $payload = pack('CN', 0, strlen($payload)) . $payload;
        return $this->client->write($streamId, $payload, $end);
    }

    /**
     * Send a message to the remote endpoint.
     *
     * @param mixed $method
     * @param mixed $message
     * @param mixed $type
     * @param mixed $user_agent
     *
     * @return mixed
     */
    private function sendMessage($method, $message, $type, $user_agent)
    {
        $request = new \Swoole\Http2\Request();
        $request->pipeline = false;
        $request->method = 'POST';
        $request->path = $method;
        $request->headers = [
            'content-type' => 'application/grpc+' . $type,
            'user-agent' => $user_agent,
            'te' => 'trailers',
        ];

        // Serialize the message based on the type
        if ($type === 'proto') {
            $payload = $message->serializeToString();
        } elseif ($type === 'json') {
            $payload = $message;
        }

        // Pack the payload and set it in the request
        $request->data = pack('CN', 0, strlen($payload)) . $payload;

        return $this->client->send($request);
    }

    /**
     * Receive data from the client.
     *
     * @return array
     * @throws ClientException
     */
    private function recvData()
    {
        try {
            if ($this->mode === Constant::GRPC_CALL) {
                $response = $this->client->recv($this->settings['timeout']);
            } else {
                $response = $this->client->read($this->settings['timeout']);
            }

            if (!$response) {
                // if ($this->client->errCode > 0) {
                //     // Log the error instead of throwing an exception
                //     error_log(Util::getErrorMessage($this->client->errCode, 9) . " {$this->client->host}:{$this->client->port}");
                // }

                Coroutine::sleep(1);
                return [0, null, false, null];
            }

            if ($response->data) {
                $data = substr($response->data, 5);
                $trailers = [
                    'grpc-status' => $response->headers['grpc-status'] ?? '0',
                    'grpc-message' => $response->headers['grpc-message'] ?? '',
                ];

                return [$response->streamId, $data, $response->pipeline, $trailers];
            }
        } catch (\Throwable $e) {
            error_log($e->getMessage());
        }

        return [0, null, false, null];
    }
    
    
    /**
     * Set the HTTP client for the gRPC client.
     *
     * @param \Swoole\Coroutine\Http2\Client $httpClient
     */
    public function setHttpClient(\Swoole\Coroutine\Http2\Client $httpClient): self
    {
        $this->client = $httpClient;
        return $this;
    }
}
