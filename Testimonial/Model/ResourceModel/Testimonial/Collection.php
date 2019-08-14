<?php
namespace LOANDT\Testimonial\Model\ResourceModel\Testimonial;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'testimonial_id';
	protected $_eventPrefix = 'testimonial_collection';
	protected $_eventObject = 'post_testimonial_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('LOANDT\Testimonial\Model\Post', 'LOANDT\Testimonial\Model\ResourceModel\Post');
	}
}