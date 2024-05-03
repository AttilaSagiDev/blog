<?php
/**
 * Copyright © 2023, Open Software License ("OSL") v. 3.0
 */

declare(strict_types=1);

namespace Space\Blog\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Space\Blog\Api\Data\ConfigInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    /**
     * @var ConfigInterface
     */
    private ConfigInterface $config;

    /**
     * Constructor
     *
     * @param Context $context
     * @param ConfigInterface $config
     */
    public function __construct(
        Context $context,
        ConfigInterface $config
    ) {
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute(): ResultInterface|ResponseInterface
    {
        if (!$this->config->isEnabled()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage('Module disabled.');
            return $resultRedirect->setPath('no-route');
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
