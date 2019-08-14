<?php
namespace LOANDT\Testimonial\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Index';

    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('LOANDT_Testimonial::index');
        $resultPage->getConfig()->getTitle()->prepend(__('List of testimonial'));
        return $resultPage;
    }
}