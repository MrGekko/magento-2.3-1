<?php

namespace Comperia\ComperiaGateway\Controller\Notification;

use Comperia\ComperiaGateway\Api\ComperiaStatusManagementInterface;
use Comperia\ComperiaGateway\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Serialize\SerializerInterface;

class Index extends Action
{
    public const NOTIFICATION_URL = 'comperia/notification';

    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var ComperiaStatusManagementInterface
     */
    private $statusManagement;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param RequestInterface $request
     * @param SerializerInterface $serializer
     * @param Data $helper
     * @param ComperiaStatusManagementInterface $comperiaStatusManagement
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        SerializerInterface $serializer,
        Data $helper,
        ComperiaStatusManagementInterface $comperiaStatusManagement
    ) {
        $this->request = $request;
        $this->serializer = $serializer;
        $this->helper = $helper;
        $this->statusManagement = $comperiaStatusManagement;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $jsonContent = $this->request->getContent();
        $content = $this->serializer->unserialize($jsonContent);

        if (!isset($content['externalId'])) {
            $this->setResponse(404, __('Empty content.'));
            return $this->_response;
        }
        if (!$this->isValidSignature($jsonContent)) {
            $this->setResponse(400, __('Failed comparission of CR-Signature and shop hash.'));
            return $this->_response;
        }

        $this->statusManagement->changeApplicationAndOrderStatus($content['externalId'], $content['status']);

        $this->setResponse(200, __('Application Status Changed'));
        return $this->_response;
    }

    /**
     * @param int $code
     * @param string $content
     * @return void
     */
    private function setResponse(int $code, string $content): void
    {
        $this->getResponse()
            ->setStatusCode($code)
            ->setContent($content);
    }

    /**
     * @param string $jsonData
     *
     * @return bool
     */
    private function isValidSignature(string $jsonData): bool
    {
        $crSignature = $this->request->getHeader('CR-Signature');
        $hash = hash('sha3-256', $this->helper->getApiKey() . $jsonData);

        return $crSignature === $hash;
    }
}
