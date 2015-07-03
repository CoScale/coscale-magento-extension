<?php
/**
 * CoScale metric interaction model
 *
 * @package CoScale_Monitor
 * @author Rian Orie <rian.orie@supportdesk.nu>
 * @created 2015-07-03
 * @version 1.0
 *
 * @method setId(int $metricId)
 * @method setDatatype(int $type)
 * @method setName(string $name)
 * @method setDescription(string $description)
 * @method setGroups(string $groups)
 * @method setUnit(string $unit)
 * @method setTags(string $tags)
 * @method setTimestamp(int $timestamp)
 * @method setUpdatedAt(string $date)
 * @method int getId()
 * @method int getDatatype()
 * @method string getName()
 * @method string getDescription()
 * @method string getGroups()
 * @method string getUnit()
 * @method string getTags()
 * @method int getTimestamp()
 * @method int getCalctype()
 * @method string getUpdatedAt()
 */ 
class CoScale_Monitor_Model_Metric extends Mage_Core_Model_Abstract
{
	/**
	 * Double datatype
	 */
	const DATATYPE_DOUBLE = 1;

	/**
	 * Result will be the difference between new and old
	 */
	const CALCTYPE_DIFFERENCE = 1;

	/**
	 * Result will be the new value
	 */
	const CALCTYPE_INSTANT = 2;

	/**
	 * Result will be average per read
	 */
	const CALCTYPE_AVERAGE = 3;

	/**
	 * Result will be percentage of the difference between new and old relative to the time interval
	 */
	const CALCTYPE_DIFFERENCE_PERCENTAGE = 4;

	/**
	 * Result will be the invers of average
	 */
	const CALCTYPE_INVERS_AVERAGE = 5;

	/**
	 * Construct the metric model
	 */
    protected function _construct()
    {
        $this->_init('coscale_monitor/metric');
    }

	/**
	 * Set the calculation type
	 *
	 * @param int $type
	 *
	 * @throws Exception
	 *
	 * @return $this
	 */
	public function setCalctype($type)
	{
		if ( ! in_array($type, array(self::CALCTYPE_DIFFERENCE, self::CALCTYPE_INSTANT, self::CALCTYPE_AVERAGE,
		                             self::CALCTYPE_DIFFERENCE_PERCENTAGE, self::CALCTYPE_INVERS_AVERAGE))) {
			throw new Exception('Calculation type should be one of the predefined values.');
		}

		return $this->setData('calctype', $type);
	}
}