<?php

namespace EvDev\UserStatus\Controller\Index;


use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;


class Index implements HttpGetActionInterface
{
    private $pageFactory;

    /**
     * Index constructor.
     * @param PageFactory $resultFactory
     */

    public function __construct(
        PageFactory $resultFactory
    ) {
        $this->pageFactory = $resultFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        return $this->pageFactory->create();
    }
}
