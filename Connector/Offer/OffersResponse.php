<?php

namespace Comperia\ComperiaGateway\Connector\Offer;

use Comperia\ComperiaGateway\Connector\RequestInterface;
use Comperia\ComperiaGateway\Connector\ResponseInterface;

class OffersResponse implements  ResponseInterface
{
    /**
     * @var string
     */
    private $status;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var array|null
     */
    private $body;

    public function getBody(): array
    {
        return $this->body;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getOffersData(): array
    {
        return $this->body;
    }

    public function __construct(string $status, RequestInterface $request, ?array $body)
    {
        $this->status = $status;
        $this->request = $request;
        $this->body = $body;

        for ($i = 0; $i < count($this->body); $i++) {
            $this->body[$i]['loanTerm'] = $this->request->getParams()['loanTerm'];
            $this->body[$i]['rrso'] = $this->body[$i]['rrso'] * 100;
            $this->body[$i]['sumAmount'] = $this->body[$i]['instalmentAmount'] * $this->body[$i]['loanTerm'] / 100;
            $this->body[$i]['instalmentAmount'] = $this->body[$i]['instalmentAmount'] / 100;
        }
    }
}