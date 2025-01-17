<?php

namespace Comperia\ComperiaGateway\Model\Connector\Transaction\Response;

use Comperia\ComperiaGateway\Api\Data\ApplicationResponseInterface;
use Magento\Framework\DataObject;
use Symfony\Component\HttpFoundation\Response;

class ApplicationResponse extends DataObject implements ApplicationResponseInterface
{
    const POSITIVE_HTTP_CODES = [
        Response::HTTP_ACCEPTED,
        Response::HTTP_OK,
        Response::HTTP_CONTINUE,
        Response::HTTP_CREATED
    ];

    /**
     * @var int
     */
    private $code;

    /**
     * ApplicationResponse constructor.
     *
     * @param int $code
     * @param array $body
     */
    public function __construct(int $code, array $body)
    {
        $data = [];
        $this->code = $code;
        if ($this->isSuccessful()) {
            $data = [
                ApplicationResponseInterface::STATUS => $body['status'],
                ApplicationResponseInterface::EXTERNAL_ID => $body['externalId'],
                ApplicationResponseInterface::REDIRECT_URI => $body['applicationUrl'],
                ApplicationResponseInterface::HREF => $body['_links']['self']['href']
            ];
        }
        parent::__construct($data);
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->getData(self::EXTERNAL_ID);
    }

    /**
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->getData(self::REDIRECT_URI);
    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->getData(self::HREF);
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return in_array($this->code, self::POSITIVE_HTTP_CODES);
    }
}
