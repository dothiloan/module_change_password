<?php
namespace LOANDT\Testimonial\Model;

class Testimonial extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'testimonial';

	protected $_cacheTag = 'testimonial';

	protected $_eventPrefix = 'testimonial';

	protected function _construct()
	{
		$this->_init('LOANDT\Testimonial\Model\ResourceModel\Testimonial');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}