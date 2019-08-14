<?php
namespace LOANDT\Customer\Controller\Test;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /*
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;
    
    /*
     * @param Magento\Framwork\App\Action\Context $context
     * @param Magento\Framework\View\Result\PageFactory $pageFactory
     **/
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $pageFactory
        )
    {
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
    }
    
    /*
     * This is main function -> this run first when load action
     */
    public function execute()
    {
       return $this->_pageFactory->create();
    }
}