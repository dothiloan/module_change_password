<?php
namespace Pfay\Contacts\Block;

class Contactslist extends \Magento\Framework\View\Element\Template
{
    protected $scope;
  /*
   * @param Magento\Framework\View\Element\Template\Context $context
   * @param array $data
   **/  
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
        \Magento\Framework\App\Config\ScopeConfigInterface $scope
        )
    {
        parent::__construct($context, $data);
        $this->scope = $scope;
    }
    
    public function _beforeToHtml()
    {
       $test = array(1,2,3,4);
       $this->setData('testdata', $test);
    }

    public function getPaymentMethod()
    {
        $methodList = $this->scope->getValue('payment');
        return $methodList;
    }
}