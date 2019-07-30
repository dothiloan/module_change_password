<?php
namespace Pfay\Contacts\Controller\Adminhtml\Test;

class Index extends \Magento\Backend\App\Action
{
    /*
     * this function will show text: hello
     **/
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}