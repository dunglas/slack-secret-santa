<?php

namespace Joli\SlackSecretSanta;

use CL\Slack\Model\User;
use CL\Slack\Payload\ChannelsInfoPayload;
use CL\Slack\Payload\ChannelsInfoPayloadResponse;
use CL\Slack\Payload\PayloadInterface;
use CL\Slack\Payload\UsersListPayload;
use CL\Slack\Payload\UsersListPayloadResponse;
use CL\Slack\Transport\ApiClient;

class UserExtractor
{
    /** @var ApiClient */
    private $apiClient;

    /**
     * @param ApiClient $apiClient
     */
    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @return User[]
     */
    public function extractAll()
    {
        $payload = new UsersListPayload();
        $payload->getResponseClass();

        /** @var $response UsersListPayloadResponse */
        $response = $this->sendPayload($payload);

        return $response->getUsers();
    }

    /**
     * @param $channelId
     *
     * @return User[]
     */
    public function extractAllFromChannel($channelId)
    {
        $payload = new ChannelsInfoPayload();
        $payload->setChannelId($channelId);

        /** @var $response ChannelsInfoPayloadResponse */
        $response = $this->sendPayload($payload);

        return $response->getChannel()->getMembers();
    }

    /**
     * @param PayloadInterface $payload
     *
     * @return \CL\Slack\Payload\PayloadResponseInterface
     */
    private function sendPayload(PayloadInterface $payload)
    {
        $response = $this->apiClient->send($payload);

        if (!$response->isOk()) {
            throw new \RuntimeException(
                sprintf('%s (%s)', $response->getErrorExplanation(), $response->getError())
            );
        }

        return $response;
    }
}
